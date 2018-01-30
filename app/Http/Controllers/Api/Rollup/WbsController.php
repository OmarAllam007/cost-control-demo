<?php

namespace App\Http\Controllers\Api\Rollup;

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
            ->selectRaw('DISTINCT activity, activity_id, code')->get();

        return $activities;
    }
}
