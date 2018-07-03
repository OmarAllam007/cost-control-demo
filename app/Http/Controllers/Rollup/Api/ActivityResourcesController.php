<?php

namespace App\Http\Controllers\Rollup\Api;

use App\BreakDownResourceShadow;
use App\Formatters\RollupResourceFormatter;
use App\Http\Controllers\Controller;
use App\WbsLevel;
use function is_numeric;
use function request;

class ActivityResourcesController extends Controller
{
    function show(WbsLevel $wbsLevel)
    {
        $this->authorize('cost_owner', $wbsLevel->project);

        $code = request('code');

        $resources = BreakDownResourceShadow::where('wbs_id', $wbsLevel->id)
            ->costOnly()
            ->where('code', $code)->orderBy('resource_name')
            ->get()->map(function ($resource) {
                return new RollupResourceFormatter($resource);
            });

        $max_code = BreakdownResourceShadow::where('project_id', $wbsLevel->project_id)
            ->where('code', $code)->where('is_rollup', true)->max('resource_code');
        $last_code = collect(explode('.', $max_code))->last();
        $next_rollup_code = intval(collect(explode('.', $max_code))->last()) + 1;
        $next_rollup_code = sprintf('%02d', $next_rollup_code);

        return compact('resources', 'next_rollup_code');
    }
}