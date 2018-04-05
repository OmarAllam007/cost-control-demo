<?php

namespace App\Http\Controllers;

use App\BreakDownResourceShadow;
use App\WbsLevel;
use function compact;
use function func_get_args;
use Illuminate\Http\Request;

use App\Http\Requests;
use const SORT_DESC;

class ActivityLogController extends Controller
{
    function show(WbsLevel $wbs, $code)
    {
        $this->authorize('actual_resources', $wbs->project);

        $periods = $wbs->project->periods()->latest('end_date')->get();

        $shadows = BreakDownResourceShadow::with('actual_resources')->where('wbs_id', $wbs->id)->where('code', $code)->get();

        $activity_name = $shadows->first()->activity;
        $budget_cost = $shadows->sum('budget_cost');
        $actual_resources = $shadows->pluck('actual_resources')->flatten(1);
        $first_upload = $actual_resources->min('created_at');
        $last_upload = $actual_resources->max('created_at');
        $actual_cost = $actual_resources->sum('cost');
        $variance = $budget_cost - $actual_cost;

        $progress = $shadows->avg('progress');
        if ($progress == 0) {
            $status = 'Not Started';
        } elseif ($progress == 100) {
            $status = 'Closed';
        } else {
            $status = 'In Progress';
        }

        return view('activity-log.show', compact(
            'wbs', 'code', 'periods', 'activity_name', 'budget_cost', 'first_upload', 'last_upload', 'actual_cost', 'status', 'variance'
        ));
    }
}
