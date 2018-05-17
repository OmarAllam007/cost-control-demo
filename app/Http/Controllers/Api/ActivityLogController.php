<?php

namespace App\Http\Controllers\Api;

use App\BreakDownResourceShadow;
use App\StoreResource;
use App\WbsLevel;
use function collect;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class ActivityLogController extends Controller
{
    function show(WbsLevel $wbs, $code)
    {
        $this->authorize('actual_resources', $wbs->project);

        $shadows = BreakDownResourceShadow::where('wbs_id', $wbs->id)
            ->with(['important_actual_resources', 'actual_resources'])
            ->where('code', $code)->get();

        $resource_ids = $shadows->pluck('resource_id', 'resource_id');

        $store_resources = StoreResource::where('budget_code', $code)
            ->whereIn('resource_id', $resource_ids)
            ->get()->groupBY('resource_id');

        $budget_resources = $shadows->groupBy('resource_id');

        $resourceLogs = $resource_ids->forget(0)->map(function($id) use ($budget_resources, $store_resources) {
            $budget = $budget_resources->get($id);
            $resource = $budget->first();
            return [
                'name' => $resource->resource_name, 'code' => $resource->resource_code,
                'budget_resources' => $budget,
                'store_resources' => $store_resources->get($id, collect())
            ];
        })->filter(function($resource) {
            return $resource['store_resources']->count() > 0;
        })->values();
    }
}
