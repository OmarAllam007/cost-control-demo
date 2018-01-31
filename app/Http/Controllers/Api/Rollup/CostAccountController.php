<?php

namespace App\Http\Controllers\Api\Rollup;

use App\BreakDownResourceShadow;
use App\WbsLevel;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class CostAccountController extends Controller
{
    function show(WbsLevel $wbsLevel, $activity_id, $cost_account)
    {
        $this->authorize('cost_owner', $wbsLevel->project);

        return BreakDownResourceShadow::where('wbs_id', $wbsLevel->id)
            ->where('activity_id', $activity_id)
            ->where('cost_account', $cost_account)
            ->pluck('cost_account', 'breakdown_id');
    }
}
