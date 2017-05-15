<?php

namespace App\Support;

use App\ActivityMap;
use App\ActualBatch;
use App\ActualResources;
use App\BreakDownResourceShadow;
use App\Jobs\UpdateResourceDictJob;
use App\ResourceCode;
use App\Resources;
use App\Unit;
use App\UnitAlias;
use function GuzzleHttp\Psr7\str;
use Illuminate\Support\Collection;

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
            $code = trim(strtolower($row[0]));
            if (!$this->activityCodes->has($code)) {
                $errors['activity']->push($row);
            }
        }

        foreach ($this->rows as $row) {
            $code = trim(strtolower($row[7]));
            if (!$this->resourcesMap->has($code)) {
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
        $errors = collect();

        $resourcesLog = collect();

        $invalid = collect();

        foreach ($this->rows as $hash => $row) {

            $activityCode = $this->activityCodes->get(trim(strtolower($row[0])));
            $resourceIds = $this->resourcesMap->get(trim(strtolower($row[7])));

            $shadowResource = BreakDownResourceShadow::where('code', $activityCode)->whereIn('resource_id', $resourceIds)->first();
            if (!$shadowResource) {
                $invalid->push($row);
                continue;
            }

            if (!$resourcesLog->has($shadowResource->id)) {
                $resourcesLog->put($shadowResource->id, collect(['resource' => $shadowResource, 'rows' => collect()]));
            }
            $row['hash'] = $hash;
            $resourcesLog->get($shadowResource->id)->get('rows')->push($row);
        }

        $this->loadUnits();
        foreach ($resourcesLog as $id => $record) {
            if ($record['rows']->count() > 1) {
                $count = $record['rows']->pluck(7)->unique()->count();
                if ($count > 1) {
                    $errors->put($id, $record);
                    continue;
                }

                foreach ($record['rows'] as $row) {
                    $unit_id = $this->unitsMap->get($row[3]);
                    if ($record['resource']->unit_id != $unit_id) {
                        $errors->put($id, $record);
                        break;
                    }
                }
            } else {
                $row = $record['rows']->first();
                $unit_id = $this->unitsMap->get(trim(strtolower($row[3])));
                if ($record['resource']->unit_id != $unit_id) {
                    $errors->put($id, $record);
                }
            }
        }

        $this->cache();

        $costIssues = new CostIssuesLog($this->batch);
        $costIssues->recordInvalid($invalid);

        if ($errors->count()) {
            return ['error' => 'physical_qty', 'errors' => $errors, 'batch' => $this->batch];
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
            $activityCode = $this->activityCodes->get(trim(strtolower($row[0])));
            $resourceIds = $this->resourcesMap->get(trim(strtolower($row[7])));
            $resources = BreakDownResourceShadow::where('code', $activityCode)->whereIn('resource_id', $resourceIds)->get();
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
            $activityCode = $this->activityCodes->get(trim(strtolower($row[0])));
            $resourceIds = $this->resourcesMap->get(trim(strtolower($row[7])));


            $shadows = BreakDownResourceShadow::where('code', $activityCode)->whereIn('resource_id', $resourceIds)
                ->whereRaw('coalesce(progress, 0) < 100')->whereRaw("coalesce(status, '') != 'closed'")
                ->get();

            if ($shadows->count() > 1) {
                $row['hash'] = $hash;
                $row['resources'] = $shadows;
                $errors->push($row);
            } elseif ($shadows->count() < 1) {
                $invalid->push($row);
            }
        }

        if ($errors->count()) {
            return ['error' => 'cost_accounts', 'errors' => $errors, 'batch' => $this->batch];
        }

        $costIssues = new CostIssuesLog($this->batch);
        $costIssues->recordInvalid($invalid);

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

        foreach ($this->rows as $row) {
            if (isset($row['resource'])) {
                $resource = $row['resource'];
                unset($row['resource']);
            } else {
                $activityCode = $this->activityCodes->get(trim(strtolower($row[0])));
                $resourceIds = $this->resourcesMap->get(trim(strtolower($row[7])));
                $resource = BreakDownResourceShadow::where('code', $activityCode)->whereIn('resource_id', $resourceIds)
                    ->whereRaw('coalesce(progress, 0) < 100')->whereRaw("coalesce(status, '') != 'closed'")
                    ->first();
            }

            if (!$resource) {
                $invalid->push($row);
                continue;
            }

            $actual_resource = ActualResources::create([
                'project_id' => $project_id, 'period_id' => $period_id, 'wbs_level_id' => $resource->wbs_id, 'batch_id' => $batch_id,
                'breakdown_resource_id' => $resource->breakdown_resource_id, 'original_code' => $row[7], 'qty' => $row[4], 'unit_price' => $row[5], 'cost' => $row[6],
                'unit_id' => $resource->unit_id, 'resource_id' => $resource->resource_id, 'doc_no' => $row[8] ?? '', 'original_data' => json_encode($row),
                'action_date' => $row[1]
            ]);

            $resource_dict->push($resource->resource_id);

            $this->actual_resources->push($actual_resource);
        }

        dispatch(new UpdateResourceDictJob($this->batch->project, $resource_dict));

        $this->rows = collect();
        $this->cache();

        $costIssues = new CostIssuesLog($this->batch);
        $costIssues->recordInvalid($invalid);

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
                $resource->cost = $resource->cost()->first();
                return $resource->cost->to_date_qty >= $resource->budget_unit;
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
            ->get(['id', 'breakdown_resource_id', 'code'])
            ->each(function ($activity) {
                $code = trim(strtolower($activity->code));
                $this->activityCodes->put($code, $code);
            });

        ActivityMap::where('project_id', $this->batch->project_id)->each(function ($mapping) {
            $code = trim(strtolower($mapping->equiv_code));
            $mappingCode = trim(strtolower($mapping->activity_code));
            if ($this->activityCodes->has($mappingCode)) {
                $this->activityCodes->put($code, $mappingCode);
            }
        });
    }

    protected function loadResourceCodes()
    {
        $this->resourcesMap = collect();

        Resources::where('project_id', $this->batch->project_id)->get(['resource_code', 'id'])->each(function (Resources $resource) {
            $code = trim(strtolower($resource->resource_code));
            if ($this->resourcesMap->has($code)) {
                $this->resourcesMap->get($code)->push($resource->id);
            } else {
                $this->resourcesMap->put($code, collect([$resource->id]));
            }
        });

        ResourceCode::where('project_id', $this->batch->project_id)->get(['id', 'resource_id', 'code'])->each(function ($resource) {
            $code = trim(strtolower($resource->code));
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
            $code = trim(strtolower($alias->name));
            $this->unitsMap->put($code, $alias->unit_id);
        });

        Unit::all()->each(function (Unit $unit) {
            $code = trim(strtolower($unit->type));
            $this->unitsMap->put($code, $unit->id);
        });
    }

    protected function cache()
    {
        $key = 'batch_' . $this->batch->id;
        \Cache::put($key, ['batch' => $this->batch, 'rows' => $this->rows, 'actual_resources' => $this->actual_resources], 1440);
    }


}