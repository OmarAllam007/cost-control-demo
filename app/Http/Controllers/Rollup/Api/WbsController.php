<?php

namespace App\Http\Controllers\Rollup\Api;

use App\BreakDownResourceShadow;
use App\WbsLevel;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class WbsController extends Controller
{
    function show(WbsLevel $wbsLevel)
    {
        $this->authorize('cost_owner', $wbsLevel->project);

        $activities = BreakDownResourceShadow::where('wbs_id', $wbsLevel->id)
            ->where('is_rollup', false)->whereNull('rolled_up_at')
            ->selectRaw('DISTINCT activity, activity_id, code')->get();

        return $activities;
    }
}
