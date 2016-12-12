<?php

namespace App\Jobs;

use App\ActivityMap;
use App\ActualResources;
use App\BreakdownResource;
use App\BreakDownResourceShadow;
use App\Period;
use App\Project;
use App\ResourceCode;
use App\Resources;
use App\Unit;
use App\UnitAlias;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ImportMaterialDataJob extends Job
{
    //<editor-fold defaultstate="collapsed" desc="Variables declaration">
    /** @var Collection */
    protected $activityMap;

    /** @var Collection */
    protected $resourceMap;

    /** @var Collection */
    protected $activityCodes;

    /** @var Collection */
    protected $resourceIds;

    /** @var Period */
    protected $active_period;

    /** @var Collection */
    protected $unitsMap;

    /** @var Project */
    protected $project;

    /** @var Collection */
    protected $data;
    //</editor-fold>

    public function __construct(Project $project, Collection $data)
    {
        $this->project = $project;
        $this->data = $data;
        $this->active_period = $project->open_period();
        $this->loadActivityMap();
        $this->loadResourceMap();
        $this->loadUnits();
    }

    public function handle()
    {
        $result = [
            'success' => 0,
            'hasIssues' => false,
            'mapping' => collect(),
            'resources' => collect(),
            'units' => collect(),
            'multiple' => collect(),
            'invalid' => collect(),
            'project' => $this->project
        ];

        // Excel date is translated to number of days since 30/12/1899
        $excelBaseDate = Carbon::create(1899, 12, 30);

        foreach ($this->data as $row) {
            $activityCode = strtolower($row[3]);
            if (!$this->activityMap->has($activityCode)) {
                // Failed to find activity (breakdown resource)
                // then add it to activity errors
                $result['mapping']->push($row);
                continue;
            }
            $activityCodes = $this->activityMap->get($activityCode);

            $resourceCode = strtolower($row[13]);
            if (!$this->resourceMap->has($resourceCode)) {
                // Resource is not found even in mapping
                // Add to resource errors
                $result['resources']->push($row);
                continue;
            }
            $resource_ids = $this->resourceMap->get($resourceCode);

            // Find the activities/breakdown resources corresponding to activity code and resources
            $breakdownResources = BreakdownResource::where('code', $activityCodes)
                ->whereIn('resource_id', $resource_ids)->get();

            /** @var \Illuminate\Database\Eloquent\Collection $breakdownResources */
            if ($breakdownResources->count() == 1) {
                // We have only one code. This is the oprimal case
                $breakdownResource = $breakdownResources->first();

                //Check unit of measure
                $unit_id = $this->unitsMap->get(mb_strtolower($row[9]));
                $resource = $breakdownResource->resource;
                if ($unit_id != $resource->unit) {
                    // Unit of measure is not matching we should ask for quantity
                    $row['resource'] = BreakDownResourceShadow::where('breakdown_resource_id', $breakdownResource->id)->first();
                    $result['units']->push($row);
                    continue;
                }

                // Optimal case everything matches, save the quantity and continue
                ActualResources::create([
                    'project_id' => $this->project->id,
                    'wbs_level_id' => $breakdownResource->breakdown->wbs_level_id,
                    'breakdown_resource_id' => $breakdownResource->id,
                    'period_id' => $this->active_period->id,
                    'qty' => abs($row[10]),
                    'unit_price' => $row[11],
                    'cost' => abs($row[12]),
                    'unit_id' => $unit_id,
                    'action_date' => $excelBaseDate->addDays($row[5])
                ]);

                ++$result['success'];
            } elseif (!$breakdownResources->count()) {
                // Nothing matches, worst case ever
                $result['invalid']->push($row);
            } else {
                // Multiple activities/breakdown resources - with the same resource found
                $breakdownResources->load('shadow');
                $row['resources'] = $breakdownResources->pluck('shadow');
                if (!$result['multiple']->has($activityCode)) {
                    $result['multiple']->put($activityCode, collect());
                }
                $result['multiple']->get($activityCode)->put($resourceCode, $row);
            }
        }

        foreach (['mapping', 'resources', 'units', 'multiple', 'invalid'] as $key) {
            if ($result[$key]->count()) {
                $result['hasIssues'] = true;
                break;
            }
        }

        // dd($result);

        return $result;
    }

    //<editor-fold defaultstate="collapsed" desc="Data loaders">
    protected function loadActivityMap()
    {
        $this->activityMap = collect();
        ActivityMap::where('project_id', $this->project->id)
            ->select('activity_code', 'equiv_code')->get()->each(function ($item) {
                $code = strtolower($item->activity_code);
                $equiv_code = strtolower($item->equiv_code);

                $this->activityMap->put($equiv_code, $code)->put($code, $code);
            });


        $this->activityCodes = collect();
        BreakdownResource::whereHas('breakdown', function ($q) {
            $q->where('project_id', $this->project->id);
        })->select(['id', 'code'])->get()->reduce(function (Collection $collection, $resource) {
            $code = strtolower($resource->code);
            if (!$collection->has($code)) {
                $collection->put($code, collect());
            }

            $collection->get($code)->push($resource->id);
            return $collection;
        }, collect());
    }

    protected function loadResourceMap()
    {
        $this->resourceMap = collect();

        Resources::select('id', 'resource_code')->get()->each(function (Resources $resource) {
            $code = strtolower($resource->resource_code);
            if (!$this->resourceMap->has($code)) {
                $this->resourceMap->put($code, collect());
            }
            $this->resourceMap->get($code)->push($resource->id);
        });

        ResourceCode::select('resource_id', 'code')->get()->each(function ($resource_code) {
            $code = strtolower($resource_code->code);
            if (!$this->resourceMap->has($code)) {
                $this->resourceMap->put($code, collect());
            }
            $this->resourceMap->get($code)->push($resource_code->resource_id);
        });
    }

    protected function loadUnits()
    {
        $this->unitsMap = collect();

        UnitAlias::all()->each(function (UnitAlias $alias) {
            $this->unitsMap->put(mb_strtolower($alias->name), $alias->unit_id);
        });

        Unit::all()->each(function (Unit $unit) {
            $this->unitsMap->put(mb_strtolower($unit->type), $unit->id);
        });
    }
    //</editor-fold>
}
