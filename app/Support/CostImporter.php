<?php

namespace App\Support;

use App\ActivityMap;
use App\ActualBatch;
use App\ActualResources;
use App\BreakDownResourceShadow;
use App\ImportantActualResource;
use App\Jobs\UpdateResourceDictJob;
use App\ResourceCode;
use App\Resources;
use App\StoreResource;
use App\Unit;
use App\UnitAlias;
use function collect;
use function explode;
use function GuzzleHttp\Psr7\str;
use Illuminate\Support\Collection;
use function json_decode;
use function mb_substr;
use function preg_split;

class CostImporter
{
    /** @var ActualBatch */
    protected $batch;

    /** @var Collection */
    protected $rows;

    /** @var Collection */
    protected $unitsMap;

    /** @var Collection */
    protected $resourcesMap;

    /** @var Collection */
    protected $activityCodes;

    /** @var Collection */
    protected $data_to_save;

    /** @var Collection */
    private $rolledUpResources;

    /** @var Collection */
    protected $actual_resources;

    function __construct(ActualBatch $batch, Collection $rows = null)
    {
        set_time_limit(600);

        $this->batch = $batch;

        if ($rows) {
            $this->rows = $rows;
            $this->cache();
        } else {
            $key = 'batch_' . $this->batch->id;
            $data = \Cache::get($key);
            $this->rows = $data['rows'];
            $this->actual_resources = $data['actual_resources'];
        }

        $this->loadActivityCodes();
        $this->loadResourceCodes();
    }

    /**
     * #E01 - Check mapping
     */
    function checkMapping()
    {
        $errors = ['activity' => collect(), 'resources' => collect()];
        foreach ($this->rows as $row) {
            $code = code_trim(strtolower($row[0]));
            if (!$this->activityCodes->has($code)) {
                $errors['activity']->push($row);
            }
        }

        foreach ($this->rows as $row) {
            $activityCode = $this->activityCodes->get(code_trim(strtolower($row[0])));
            if ($this->rolledUpResources->has($activityCode)) {
                continue;
            }

            $code = strtolower(code_trim($row[7]));
            if (!$this->resourcesMap->has($code) && !$this->rolledUpResources->has($code)) {
                $errors['resources']->push($row);
            }
        }

        if ($errors['activity']->count() || $errors['resources']->count()) {
            return ['error' => 'mapping', 'errors' => $errors, 'batch' => $this->batch];
        }

        return $this->checkPhysicalQty();
    }

    /**
     * #E02 - Physical Qty
     */
    public function checkPhysicalQty()
    {
        $parser = new PhysicalQtyParser($this->batch, $this->rows);
        $errors = $parser->handle();

        $this->cache();

        if ($errors['resources']->count()) {
            return ['error' => 'physical_qty', 'errors' => $errors['resources'], 'batch' => $this->batch];
        }

        return $this->checkClosed();
    }

    /**
     * #E03 - Closed Resources
     */
    public function checkClosed()
    {
        $errors = collect();

        foreach ($this->rows as $hash => $row) {
            if (isset($row['resource'])) {
                $resource = $row['resource'];
                if (strtolower($resource->status) == 'closed' || $resource->progress == 100) {
                    $errors->push($resource);
                }
                continue;
            }

            $activityCode = $this->activityCodes->get(code_trim(strtolower($row[0])));
            $query = BreakDownResourceShadow::where('code', $activityCode)->whereNull('rolled_up_at');

            $resource_code = code_trim(strtolower($row[7]));
            if ($this->resourcesMap->has($resource_code)) {
                $resourceIds = $this->resourcesMap->get($resource_code);
                $query->whereIn('resource_id', $resourceIds);
                if (!empty($row['9'])) {
                    $query->where('cost_account', $row[9]);
                }
            } elseif ($this->rolledUpResources->has($resource_code)) {
                $query->where('resource_code', $resource_code);
            }

            $resources = $query->get();

            foreach ($resources as $resource) {
                if (strtolower($resource->status) == 'closed' || $resource->progress == 100) {
                    $errors->push($resource);
                }
            }
        }

        if ($errors->count()) {
            return ['error' => 'closed', 'errors' => $errors, 'batch' => $this->batch];
        }

        return $this->checkMultipleCostAccounts();
    }

    /**
     * E04 - One resource on multiple cost accounts
     */
    public function checkMultipleCostAccounts()
    {
        $errors = collect();

        $invalid = collect();
        foreach ($this->rows as $hash => $row) {
            if (isset($row['resource'])) {
                continue;
            }

            $activityCode = $this->activityCodes->get(code_trim(strtolower($row[0])));
            $query = BreakDownResourceShadow::where('code', $activityCode)
                ->whereNull('rolled_up_at')
                ->whereRaw('coalesce(progress, 0) < 100')
                ->whereRaw("coalesce(status, '') != 'closed'");

            $resource_code = code_trim(strtolower($row[7]));
            if ($this->resourcesMap->has($resource_code)) {
                $resourceIds = $this->resourcesMap->get($resource_code);
                $query->whereIn('resource_id', $resourceIds);

                if (!empty($row[9])) {
                    $query->where('cost_account', $row[9]);
                }
            } elseif ($this->rolledUpResources->has($resource_code)) {
                $query->where('resource_code', $resource_code);
            }

            $shadows = $query->get();

            if ($shadows->count() > 1) {
                $row['hash'] = $hash;
                $row['resources'] = $shadows;
                $errors->push($row);
            } elseif ($shadows->count() < 1) {
                StoreResource::where('id', $hash)->delete();
                $this->rows->forget($hash);
                $invalid->push($row);
            }
        }

        if ($errors->count()) {
            return ['error' => 'cost_accounts', 'errors' => $errors, 'batch' => $this->batch];
        }

        if ($invalid->count()) {
            $costIssues = new CostIssuesLog($this->batch);
            $costIssues->recordInvalid($invalid);
        }

        return $this->save();
    }

    /**
     * #E05 - Progress
     *
     * Save the data and check if we need to provide progress
     */
    function save()
    {
        $project_id = $this->batch->project_id;
        $period_id = $this->batch->project->open_period()->id;
        $batch_id = $this->batch->id;

        $this->actual_resources = collect();
        $invalid = collect();

        $resource_dict = collect();

        foreach ($this->rows as $hash => $row) {
            if (isset($row['resource'])) {
                $resource = $row['resource'];
                unset($row['resource']);
            } else {
                $activityCode = $this->activityCodes->get(code_trim(strtolower($row[0])));
                $query = BreakDownResourceShadow::where('code', $activityCode)
                    ->whereRaw('coalesce(progress, 0) < 100')->whereRaw("coalesce(status, '') != 'closed'");
                $resource_code = strtolower(code_trim($row[7]));
                if ($this->resourcesMap->has($resource_code)) {
                    $resourceIds = $this->resourcesMap->get($resource_code);
                    $query->whereIn('resource_id', $resourceIds);
                    if (!empty($row[9])) {
                        $query->where('cost_account', $row[9]);
                    }
                } elseif ($this->rolledUpResources->has($resource_code)) {
                    $query->where('resource_code', $resource_code);
                }

                $resource = $query->first();
            }

            if (!$resource) {
                StoreResource::where('id', $hash)->delete();
                $this->rows->forget($hash);
                $invalid->push($row);
                continue;
            }

            $attributes = [
                'project_id' => $project_id, 'period_id' => $period_id, 'wbs_level_id' => $resource->wbs_id, 'batch_id' => $batch_id,
                'breakdown_resource_id' => $resource->breakdown_resource_id, 'original_code' => $row[7], 'qty' => $row[4], 'unit_price' => $row[5], 'cost' => $row[6],
                'unit_id' => $resource->unit_id, 'resource_id' => $resource->resource_id, 'doc_no' => $row[8] ?? '', 'original_data' => json_encode($row),
                'action_date' => $row[1]
            ];

            if ($resource->is_rollup || !$resource->rolled_up_at) {
                $actual_resource = ActualResources::create($attributes);
                $this->actual_resources->push($actual_resource);
                $attributes = [
                    'budget_code' => $resource->code, 'resource_id' => $resource->resource_id,
                    'actual_resource_id' => $actual_resource->id
                ];

                $store_resource = StoreResource::find($hash);
                $store_resource->update($attributes);
                if ($store_resource->row_ids) {
                    StoreResource::whereIn('id', json_decode($store_resource->row_ids))->update($attributes);
                }
            } else {
                ImportantActualResource::create($attributes);
                StoreResource::where('id', $hash)
                    ->update(['budget_code' => $resource->code, 'resource_id' => $resource->resource_id]);
            }


            if (!$resource->is_rollup) {
                $resource_dict->push($resource->resource_id);
            }
        }

        dispatch(new UpdateResourceDictJob($this->batch->project, $resource_dict));

        $this->rows = collect();
        $this->cache();

        if ($invalid->count()) {
            $costIssues = new CostIssuesLog($this->batch);
            $costIssues->recordInvalid($invalid);
        }

        return $this->checkProgress();
    }

    function checkProgress()
    {
        if ($this->actual_resources->count()) {
            $breakdown_resource_ids = $this->actual_resources->pluck('breakdown_resource_id');
        } else {
            $breakdown_resource_ids = ActualResources::where('batch_id', $this->batch->id)->pluck('breakdown_resource_id');
        }

        $errors = BreakDownResourceShadow::with('cost')
            ->whereIn('breakdown_resource_id', $breakdown_resource_ids)->get()
            ->filter(function ($resource) {
                if ($resource->qty_to_date > $resource->budget_unit) {
                    return true;
                }

                return $resource->std_activity->isGeneral();
            });

        if ($errors->count()) {
            return ['error' => 'progress', 'errors' => $errors, 'batch' => $this->batch];
        }

        return $this->checkStatus();
    }

    function checkStatus()
    {
        if (count($this->actual_resources)) {
            $breakdown_ids = $this->actual_resources->pluck('breakdown_resource_id');
        } else {
            $breakdown_ids = ActualResources::where('batch_id', $this->batch->id)->pluck('breakdown_resource_id');
        }

        $resources = BreakDownResourceShadow::with('cost')->whereIn('breakdown_resource_id', $breakdown_ids)->get();

        if (!$resources->count()) {
            return ['error' => 'no_resources', 'batch' => $this->batch];
        }

        return ['error' => 'status', 'errors' => $resources, 'batch' => $this->batch];
    }

    protected function loadActivityCodes()
    {
        $this->activityCodes = collect();

        BreakDownResourceShadow::where('project_id', $this->batch->project_id)
            ->selectRaw('DISTINCT code')->get()->each(function ($activity) {
                $code = code_trim(strtolower($activity->code));
                $this->activityCodes->put($code, $code);
            });

        ActivityMap::where('project_id', $this->batch->project_id)->each(function ($mapping) {
            $code = code_trim(strtolower($mapping->equiv_code));
            $mappingCode = code_trim(strtolower($mapping->activity_code));
            if ($this->activityCodes->has($mappingCode)) {
                $this->activityCodes->put($code, $mappingCode);
            }
        });
    }

    protected function loadResourceCodes()
    {
        $this->rolledUpResources = BreakDownResourceShadow::where('project_id', $this->batch->project_id)
            ->where('is_rollup', 1)->get()->reduce(function (Collection $mapping, $resource) {
                $code = strtolower($resource->resource_code);
                $mapping->put($code, $code);
                return $mapping;
            }, collect());

        $this->resourcesMap = collect();
        Resources::where('project_id', $this->batch->project_id)
            ->get(['resource_code', 'id'])->each(function (Resources $resource) {
                $code = code_trim(strtolower($resource->resource_code));
                if ($this->resourcesMap->has($code)) {
                    $this->resourcesMap->get($code)->push($resource->id);
                } else {
                    $this->resourcesMap->put($code, collect([$resource->id]));
                }
            });

        ResourceCode::where('project_id', $this->batch->project_id)
            ->get(['id', 'resource_id', 'code'])->each(function ($resource) {
                $code = code_trim(strtolower($resource->code));
                if ($this->resourcesMap->has($code)) {
                    $this->resourcesMap->get($code)->push($resource->resource_id);
                } else {
                    $this->resourcesMap->put($code, collect([$resource->resource_id]));
                }
            });
    }


    protected function loadUnits()
    {
        $this->unitsMap = collect();

        UnitAlias::all()->each(function (UnitAlias $alias) {
            $code = code_trim(strtolower($alias->name));
            $this->unitsMap->put($code, $alias->unit_id);
        });

        Unit::all()->each(function (Unit $unit) {
            $code = code_trim(strtolower($unit->type));
            $this->unitsMap->put($code, $unit->id);
        });
    }

    protected function cache()
    {
        $this->preProcess();

        $key = 'batch_' . $this->batch->id;
        \Cache::put($key, ['batch' => $this->batch, 'rows' => $this->rows, 'actual_resources' => $this->actual_resources], 1440);
    }

    protected function preProcess()
    {
        $newRows = collect();
        $this->rows->map(function ($data, $hash) {
            $data['hash'] = $hash;
            return $data;
        })->groupBy(0)->each(function (Collection $group) use ($newRows) {
            $group->groupBy(7)->each(function (Collection $group) use ($newRows) {
                $group->groupBy(3)->each(function (Collection $costAccounts) use ($newRows) {
                    $costAccounts->groupBy(9)->each(function (Collection $entries) use ($newRows) {
                        $hasResources = $entries->pluck('resource')->filter()->count();
                        if ($hasResources) {
                            foreach ($entries as $entry) {
                                $newRows->put($entry['hash'], $entry);
                            }
                        } else {
                            $first = $entries->first();

                            $first[4] = $entries->sum(4);
                            $first[6] = $entries->sum(6);
                            if ($first[4]) {
                                $first[5] = $first[6] / $first[4];
                            } else {
                                $first[5] = 0;
                            }

                            $newRows->put($first['hash'], $first);
                        }

                    });
                });
            });
        });

        $newRows = $newRows->keyBy('hash');
        return $this->rows = $newRows;
    }

    private function getResources($row)
    {
        $store_activity = code_trim(strtolower($row[0]));
        $budget_activity = $this->activityCodes->get($store_activity);

        $store_resource = code_trim(strtolower($row[7]));
        $budget_resources = $this->resourcesMap->get($store_resource);

        $resources = BreakDownResourceShadow::whereProjectId($this->batch->project_id)
            ->whereCode($budget_activity)
            ->whereIn('resource_code', $budget_resources)->get();

        if (!$resources) {
            return false;
        }


    }
}