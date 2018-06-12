<?php

namespace App\Http\Controllers\Rollup\Api;

use App\BreakdownResource;
use App\BreakDownResourceShadow;
use App\WbsLevel;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Pagination\LengthAwarePaginator;

class WbsController extends Controller
{
    function show(WbsLevel $wbsLevel)
    {
        $this->authorize('cost_owner', $wbsLevel->project);
        
        $baseQuery = BreakDownResourceShadow::whereIn('wbs_id', $wbsLevel->getChildrenIds())
            ->where('is_rollup', false)->whereNull('rolled_up_at')
            ->selectRaw('DISTINCT project_id, wbs_id, activity as name, activity_id, code')
            ->orderBy('name')->orderBy('code');
        
        $baseQuery->when(request('term'), function($query) {
            $query->where(function($q) {
                $term = '%' . request('term') . '%';
                $q->where('code', 'like', $term)->orWhere('activity', 'like', $term);
            });
        });

        /** @var LengthAwarePaginator $activity */
        $pagination = \DB::table(\DB::raw('(' . $baseQuery->toSql() . ') as data'))
            ->mergeBindings($baseQuery->getQuery())->paginate(15);

        return [
            'lastPage' => $pagination->lastPage(),
            'perPage' => $pagination->perPage(),
            'total' => $pagination->currentPage(),
            'data' => $pagination->map(function($activity) {
                $max_code = BreakdownResourceShadow::where('project_id', $activity->project_id)
                    ->where('code', $activity->code)->where('is_rollup', true)->max('resource_code');
                $next_rollup_code = (collect(explode('.', $max_code))->last()?:0) + 1;
                $activity->next_rollup_code = sprintf('%02d', $next_rollup_code);
                return $activity;
            })
        ];
    }
}
