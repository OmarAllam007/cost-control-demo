<?php
/**
 * Created by PhpStorm.
 * User: hazem
 * Date: 12/2/18
 * Time: 1:59 PM
 */

namespace App\Support;


use App\ActivityMap;
use App\ActualBatch;
use App\BreakDownResourceShadow;
use App\ResourceCode;
use App\Resources;
use App\UnitAlias;
use Illuminate\Support\Collection;

class PhysicalQtyParser
{
    /** @var Collection */
    protected $errors;

    /** @var Collection */
    protected $resourcesMap;

    /** @var Collection */
    protected $activityCodes;

    /** @var Collection */
    protected $rollupResourcesMap;

    /** @var ActualBatch */
    private $batch;

    /** @var Collection */
    private $rows;

    /** @var Collection */
    private $physicalMapping;

    public function __construct($batch, $rows)
    {
        $this->batch = $batch;
        $this->rows = $rows;
        $this->errors = collect(['invalid' => collect(), 'resources' => collect()]);

        $this->physicalMapping = collect();

        $this->loadActivityCodes();
        $this->loadResourceCodes();
    }

    function handle()
    {
        foreach ($this->rows as $row) {
            $this->handlePhysicalQty($row);
        }

        if ($this->errors['invalid']->count()) {
            $costIssues = new CostIssuesLog($this->batch);
            $costIssues->recordInvalid($this->errors['invalid']);

            $this->errors['invalid']->each(function ($row) {
                $this->rows->forget($row['hash']);
            });
        }

        $this->errors['resources'] = $this->physicalMapping->filter(function (Collection $group) {
            if ($group->get('rows')->count() > 1) {
                return true;
            }

            $row = $group->get('rows')->first();
            $resource = $group->get('resource');

            if ($resource->is_rollup) {
                return true;
            }

            return $this->checkUnitOfMeasure($resource, $row);
        })->keyBy('hash');

        return $this->errors;
    }

    function handlePhysicalQty($row)
    {
        $store_activity = trim(strtolower($row[0]));
        $budget_activity = $this->activityCodes->get($store_activity);

        $store_resource = trim(strtolower($row[7]));
        $budget_resources = $this->resourcesMap->get($store_resource, collect());
        $rollup_resource = $this->rollupResourcesMap->get($store_resource);

        $query = BreakDownResourceShadow::whereProjectId($this->batch->project_id)
            ->whereCode($budget_activity)
            ->when($budget_resources->count(), function ($query) use ($budget_resources) {
                return $query->whereIn('resource_id', $budget_resources);
            })->when($rollup_resource, function ($query) use ($rollup_resource) {
                return $query->where('id', $rollup_resource);
            })->orderByRaw('coalesce(important, 0) DESC');

        $resource = $query->first();

        if (!$resource) {
            $this->errors['invalid']->push($row);
            return false;
        }

        $this->mapResource($resource, $row);

        return true;
    }

    protected function loadActivityCodes()
    {
        $this->activityCodes = collect();

        BreakDownResourceShadow::where('project_id', $this->batch->project_id)
            ->selectRaw('DISTINCT code')->each(function ($activity) {
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
        $this->resourcesMap = Resources::where('project_id', $this->batch->project_id)
            ->get(['resource_code', 'id'])->keyBy(function ($resource) {
                return strtolower($resource->resource_code);
            })->map(function (Resources $resource) {
                return collect([$resource->id]);
            });

        ResourceCode::where('project_id', $this->batch->project_id)
            ->get(['id', 'resource_id', 'code'])->reduce(function (Collection $map, $resource) {
                $code = trim(strtolower($resource->code));
                if ($map->has($code)) {
                    $map->get($code)->push($resource->resource_id);
                } else {
                    $map->put($code, collect([$resource->resource_id]));
                }

                return $map;
            }, $this->resourcesMap);

        $this->rollupResourcesMap = BreakDownResourceShadow::whereIsRollup(true)->get()->keyBy(function ($resource) {
            return strtolower($resource->resource_code);
        })->map(function ($resource) {
            return $resource->id;
        });
    }


    private function mapResource($resource, $row)
    {
        if ($resource->rolled_up_at) {
            $breakdown_resource_id = $resource->rollupResource->breakdown_resource_id;
            if (!$this->physicalMapping->has($breakdown_resource_id)) {
                $this->physicalMapping->put($breakdown_resource_id, collect([
                    'resource' => $resource->rollupResource,
                    'rows' => collect(),
                    'hash' => $resource->rollupResource->id
                ]));
            }

            $this->physicalMapping->get($breakdown_resource_id)->get('rows')->put($row['hash'], $row);
        }

        if (!$resource->rolled_up_at || ($resource->important && $resource->rollupResource->isActivityRollup())) {
            $breakdown_resource_id = $resource->breakdown_resource_id;
            if (!$this->physicalMapping->has($breakdown_resource_id)) {
                $this->physicalMapping->put($breakdown_resource_id, collect([
                    'resource' => $resource, 'rows' => collect(), 'hash' => $resource->id
                ]));
            }

            $this->physicalMapping->get($breakdown_resource_id)->get('rows')->put($row['hash'], $row);
        }
    }

    private function checkUnitOfMeasure($resource, $row)
    {
        $storeUnit = strtolower(trim($row[3]));
        $budgetUnit = strtolower(trim($resource->measure_unit));

        if ($storeUnit == $budgetUnit) {
            return false;
        }

        return !UnitAlias::whereUnitId($resource->unit_id)->pluck('name')->map(function ($unit) {
            return strtolower(trim($unit));
        })->contains($storeUnit);
    }
}