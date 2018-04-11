<?php

namespace App\Http\Controllers\Rollup\Api;

use App\BreakDownResourceShadow;
use App\Formatters\RollupResourceFormatter;
use App\Http\Controllers\Controller;
use App\WbsLevel;

class ActivityResourcesController extends Controller
{
    function show(WbsLevel $wbsLevel, $code)
    {
        $this->authorize('cost_owner', $wbsLevel->project);

        return BreakDownResourceShadow::where('wbs_id', $wbsLevel->id)
            ->canBeRolled()
            ->where('code', $code)->get()->map(function ($resource) {
                return new RollupResourceFormatter($resource);
            });
    }
}