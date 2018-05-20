<?php

namespace App\Http\Controllers\Api;

use App\BreakDownResourceShadow;
use App\StoreResource;
use App\WbsLevel;
use function collect;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Collection;

class ActivityLogController extends Controller
{
    function show(WbsLevel $wbs, $code)
    {
        $this->authorize('actual_resources', $wbs->project);

        $shadows = BreakDownResourceShadow::where('wbs_id', $wbs->id)
            ->with(['important_actual_resources', 'actual_resources'])
            ->where('resource_id', '<>', 0)
            ->where('code', $code)->get();

        $resource_ids = $shadows->pluck('resource_id', 'resource_id');

        $store_resources = StoreResource::where('budget_code', $code)
            ->whereIn('resource_id', $resource_ids)
            ->get()->groupBY('resource_id');

        $budget_resources = $shadows->groupBy('resource_id');

        /** @var Collection $resourceLogs */
        $resourceLogs = $resource_ids->map(function($id) use ($budget_resources, $store_resources) {
            $budget = $budget_resources->get($id);
            $resource = $budget->first();
            return [
                'name' => $resource->resource_name, 'code' => $resource->resource_code,
                'budget_resources' => $budget, 'rollup' => false,
                'store_resources' => $store_resources->get($id, collect())
            ];
        });

        // Rollup resources
        $rollupLogs = BreakDownResourceShadow::where('wbs_id', $wbs->id)
            ->with(['actual_resources'])->where('resource_id', 0)
            ->where('code', $code)->get()->map(function($resource) use ($resourceLogs) {
                $budget_resources = BreakDownResourceShadow::where('rollup_resource_id', $resource->id)->get();
                $resource->actual_resources->dd();
                $store_resources = StoreResource::whereIn('actual_resource_id', $resource->actual_resources->pluck('id'))->get();
                return [
                    'name' => $resource->resource_name, 'code' => $resource->resource_code,
                    'budget_resources' => $budget_resources, 'store_resources' => $store_resources,
                    'rollup' => true
                ];
            });

        return $resourceLogs->merge($rollupLogs)->values();
    }
}
