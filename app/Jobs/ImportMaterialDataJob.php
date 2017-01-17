<?php

namespace App\Jobs;

use App\ActivityMap;
use App\ActualBatch;
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

    /** @var ActualBatch */
    private $batch;

    public function __construct(Project $project, Collection $data, ActualBatch $batch)
    {
        $this->project = $project;
        $this->data = $data;
        $this->active_period = $project->open_period();
        $this->loadActivityMap();
        $this->loadResourceMap();
        $this->loadUnits();
        $this->batch = $batch;
    }

    //</editor-fold>

    public function handle()
    {
        $result = [
            'success' => 0,
            'hasIssues' => false,
            'mapping' => collect(['resources' => collect(), 'activity' => collect()]),
            'resources' => collect(),
            'closed' => collect(),
            'units' => collect(),
            'multiple' => collect(),
            'invalid' => collect(),
            'to_import' => collect(),
            'project' => $this->project,
            'batch' => $this->batch,
        ];

        $resource_dict = collect();
        foreach ($this->data as $row) {
            $activityCode = strtolower($row[0]);

            if (!$this->activityMap->has($activityCode)) {
                // Failed to find activity (breakdown resource)
                // then add it to activity errors
                $result['mapping']['activity']->push($row);
                continue;
            }
            $activityCodes = $this->activityMap->get($activityCode);

            $resourceCode = strtolower($row[7]);
            if (!$this->resourceMap->has($resourceCode)) {
                // Resource is not found even in mapping
                // Add to mapping resource errors
                $result['mapping']['resources']->push($row);
                continue;
            }
            $resource_ids = $this->resourceMap->get($resourceCode);

            // Check unit of measure
            $unit_resources = Resources::find($resource_ids->toArray());
            if ($unit_resources && $unit_resources->count() == 1) {
                $unit_resource =$unit_resources->first();
                $resource_unit_id = $unit_resource->unit;
                $store_unit_id = $this->unitsMap->get(mb_strtolower($row[3]));
                if ($resource_unit_id != $store_unit_id) {
                    // Unit of measure is not matching we should ask for quantity
                    $row['unit_resource'] = $unit_resource;
                    $result['units']->push($row);
                    continue;
                }
            }

            if (!empty($row['resource'])) {
                $breakdownResources = collect([$row['resource']->breakdown_resource]);
            } else {
                // Find the activities/breakdown resources corresponding to activity code and resources
                $breakdownResources = BreakdownResource::where('code', $activityCodes)
                    ->whereIn('resource_id', $resource_ids)->get();
            }

            /** @var \Illuminate\Database\Eloquent\Collection $breakdownResources */
            if ($breakdownResources->count() == 1) {
                // We have only one code. This is the optimal case
                $breakdownResource = $breakdownResources->first();

                // If the resource is closed we should ask to open it first
                if ($breakdownResource->shadow->status == 'Closed') {
                    $row['resource'] = $breakdownResource->shadow;
                    $result['closed']->push($row);
                    continue;
                }

                //Check unit of measure
                $unit_id = $this->unitsMap->get(mb_strtolower($row[3]));
                $resource = $breakdownResource->resource;
                if ($unit_id != $resource->unit) {
                    // Unit of measure is not matching we should ask for quantity
                    $row['resource'] = $breakdownResource->shadow;
                    $result['units']->push($row);
                    continue;
                }

                // Optimal case everything matches, save the quantity and continue
                $result['to_import']->push([
                    'project_id' => $this->project->id,
                    'wbs_level_id' => $breakdownResource->breakdown->wbs_level_id,
                    'breakdown_resource_id' => $breakdownResource->id,
                    'period_id' => $this->active_period->id,
                    'original_code' => $row[7],
                    'resource_id' => $resource->id,
                    'qty' => $row[4],
                    'unit_price' => $row[5],
                    'cost' => $row[6],
                    'unit_id' => $unit_id,
                    'batch_id' => $this->batch->id,
                    // Excel date is translated to number of days since 30/12/1899
                    'action_date' => Carbon::create(1899, 12, 31)->addDays($row[1]),
                    'doc_no' => $row[8],
                    'original_data' => $row
                ]);

                $resource_dict->push($resource->id);

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

        /*$multiple_resources_ids = [];
        $result['to_import']->groupBy('breakdown_resource_id')->each(function($resources, $breakdown_resource_id) use ($result, $multiple_resources_ids) {
            $resource_count = $resources->pluck('original_code', 'original_code')->count();
            if ($resource_count > 1) {
                $result['resources']->push([
                    'target' => BreakDownResourceShadow::where('breakdown_resource_id', $breakdown_resource_id)->first(),
                    'resources' => $resources
                ]);

                $multiple_resources_ids[$breakdown_resource_id] = $breakdown_resource_id;
            }
        });

        $result['to_import'] = $result['to_import']->filter(function($resource) use ($multiple_resources_ids) {
            return !isset($multiple_resources_ids[$resource['breakdown_resource_id']]);
        });*/

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


        $this->activityCodes =
        BreakdownResource::whereHas('breakdown', function ($q) {
            $q->where('project_id', $this->project->id);
        })->select(['id', 'code'])->get()->each(function ($resource) {
            $code = strtolower($resource->code);
            $this->activityMap->put($code, $code);
        });
    }

    protected function loadResourceMap()
    {
        $this->resourceMap = collect();


        Resources::select('id', 'resource_code')
            ->where('project_id', $this->project->id)
            ->get()->each(function (Resources $resource) {
                $code = strtolower($resource->resource_code);
                if (!$this->resourceMap->has($code)) {
                    $this->resourceMap->put($code, collect());
                }
                $this->resourceMap->get($code)->push($resource->id);
            });

        $project_resource_map = Resources::where('project_id', $this->project->id)->pluck('id', 'resource_id');
        ResourceCode::select('resource_id', 'code')->get()
            ->each(function ($resource_code) use ($project_resource_map) {
                $code = strtolower($resource_code->code);
                if (!$this->resourceMap->has($code)) {
                    $this->resourceMap->put($code, collect());
                }

                $resource_id = $resource_code->resource_id;
                if ($project_resource_map->has($resource_code->resource_id)) {
                    $resource_id = $project_resource_map->get($resource_code->resource_id);
                }

                $this->resourceMap->get($code)->push($resource_id);
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
