<?php

namespace App\Http\Controllers\Rollup\Api;

use App\BreakDownResourceShadow;
use App\WbsLevel;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Access\AuthorizationException;

class CostAccountController extends Controller
{
    function show(WbsLevel $wbsLevel, $breakdown_id)
    {
        $this->authorize('cost_owner', $wbsLevel->project);

        return BreakDownResourceShadow::where('wbs_id', $wbsLevel->id)
            ->where('breakdown_id', $breakdown_id)
            ->where('is_rollup', false)->whereNull('rolled_up_at')
            ->where('show_in_cost', 1)
            ->selectRaw('breakdown_resource_id as id, resource_code as code, resource_name as name, remarks')
            ->selectRaw('budget_unit, important')
            ->get()->map(function($resource) {
                $resource->selected = false;
                return $resource;
            });
    }
}
