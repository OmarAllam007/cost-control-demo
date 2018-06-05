<?php

namespace App\Http\Controllers\Api;

use App\BreakdownResource;
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
    function show(WbsLevel $wbs)
    {
        $this->authorize('actual_resources', $wbs->project);

        $code = request('code');

        $isActivityRollup = BreakDownResourceShadow::where('wbs_id', $wbs->id)
            ->where('code', $code)->where('is_rollup', true)
            ->whereRaw('code = resource_code')->exists();

        $shadows = BreakDownResourceShadow::where('wbs_id', $wbs->id)
            ->where('code', $code)
            ->where('resource_id', '<>', 0)
            ->when($isActivityRollup, function($q) {
                return $q->where('important', 1);
            })->when(!$isActivityRollup, function($q) {
                return $q->where('show_in_cost', 1);
            })->with(['important_actual_resources', 'actual_resources'])
            ->get();

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
        })->filter(function ($log) {
            return $log['store_resources']->count();
        });

        // Rollup resources
        $rollupLogs = BreakDownResourceShadow::where('wbs_id', $wbs->id)
            ->with(['actual_resources'])->where('is_rollup', true)
            ->where('code', $code)->get()->map(function($resource) use ($resourceLogs) {
                $budget_resources = BreakDownResourceShadow::where('rollup_resource_id', $resource->id)->get();
                $store_resources = StoreResource::whereIn('actual_resource_id', $resource->actual_resources->pluck('id'))
                    ->whereNull('row_ids')->get();

                return [
                    'name' => $resource->resource_name, 'code' => $resource->resource_code,
                    'budget_resources' => $budget_resources, 'store_resources' => $store_resources,
                    'actual_resources' => $resource->actual_resources,
                    'rollup' => true, 'rollup_resource' => $resource
                ];
            });

        return $resourceLogs->merge($rollupLogs)->values();
    }
}
