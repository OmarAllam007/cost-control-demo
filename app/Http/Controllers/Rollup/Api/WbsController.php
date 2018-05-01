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

        $baseQuery = BreakDownResourceShadow::whereIn('wbs_id', $wbsLevel->getChildrenIds())
            ->where('is_rollup', false)->whereNull('rolled_up_at')
            ->selectRaw('DISTINCT wbs_id, activity as name, activity_id, code')
            ->orderBy('name')->orderBy('code');

        $activities = \DB::table(\DB::raw('(' . $baseQuery->toSql() . ') as data'))
            ->mergeBindings($baseQuery->getQuery())->paginate(10);

        return $activities;
    }
}
