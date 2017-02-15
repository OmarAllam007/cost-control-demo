<?php

namespace App\Support;

use App\ActivityMap;
use App\ActualBatch;
use App\BreakDownResourceShadow;
use App\ResourceCode;
use App\Resources;
use App\Unit;
use App\UnitAlias;
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

    function __construct(ActualBatch $batch, Collection $rows = null)
    {
        set_time_limit(600);

        $this->batch = $batch;

        if ($rows) {
            $this->data_to_save = collect();
            $this->rows = $rows;
            $this->cache();
        } else {
            $key = 'batch_' . $this->batch->id;
            $data = \Cache::get($key);
            $this->rows = $data['rows'];
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
                $errors['activity']->push($row[0]);
            }
        }

        foreach ($this->rows as $row) {
            $code = trim(strtolower($row[7]));
            if (!$this->activityCodes->has($code)) {
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

        foreach ($this->rows as $hash => $row) {

            $activityCode = $this->activityCodes->get(trim(strtolower($row[0])));
            $resourceIds = $this->resourcesMap->get(trim(strtolower($row[7])));

            $shadowResource = BreakDownResourceShadow::where('code', $activityCode)->whereIn('resource_id', $resourceIds)->first();
            if (!$shadowResource) {
                // TODO: Log invalid records
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
                    conitnue;
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
            $activityCode = $this->activityCodes->get($row[0]);
            $resourceIds = $this->resourcesMap->get($row[7]);

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

        foreach ($this->rows as $hash => $row) {
            $activityCode = $this->activityCodes->get($row[0]);
            $resourceIds = $this->resourcesMap->get($row[7]);

            $shadows = BreakDownResourceShadow::where('code', $activityCode)->whereIn('resource_id', $resourceIds)->get();
            if ($shadows->count() > 1) {
                $row['hash'] = $hash;
                $row['resources'] = $shadows;
                $errors->push($row);
            }
        }

        if ($errors->count()) {
            return ['error' => 'cost_accounts', 'errors' => $errors, 'batch' => $this->batch];
        }

        return $this->saveAndCheckProgress();
    }

    function saveAndCheckProgress()
    {

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
        \Cache::put($key, ['batch' => $this->batch, 'rows' => $this->rows, 'data_to_save' => $this->data_to_save], 1440);
    }
}