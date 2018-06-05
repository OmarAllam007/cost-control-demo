<?php

namespace App\Http\Controllers\Rollup\Api;

use App\BreakDownResourceShadow;
use App\Formatters\RollupResourceFormatter;
use App\Http\Controllers\Controller;
use App\WbsLevel;
use function request;

class ActivityResourcesController extends Controller
{
    function show(WbsLevel $wbsLevel)
    {
        $this->authorize('cost_owner', $wbsLevel->project);

        $code = request('code');

        return BreakDownResourceShadow::where('wbs_id', $wbsLevel->id)
            ->costOnly()
            ->where('code', $code)->orderBy('resource_name')
            ->get()->map(function ($resource) {
                return new RollupResourceFormatter($resource);
            });
    }
}