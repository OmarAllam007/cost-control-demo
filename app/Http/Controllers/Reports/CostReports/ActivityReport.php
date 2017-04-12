<?php

namespace App\Http\Controllers\Reports\CostReports;

use App\MasterShadow;
use App\Period;
use App\Project;
use App\WbsLevel;
use Illuminate\Database\Eloquent\Builder;

class ActivityReport
{
    /** @var Project */
    protected $project;

    /** @var Period */
    protected $period;

    function __construct(Period $period)
    {
        $this->project = $period->project;
        $this->period = $period;
    }

    function run()
    {
        $project = $this->period->project;

        $tree = $this->buildTree();

        $periods = $this->project->periods()->readyForReporting()->orderBy('name')->pluck('name', 'id');

        $activities = MasterShadow::forPeriod($this->period)->orderBy('activity')->selectRaw('DISTINCT activity')->pluck('activity');

        return view('reports.cost-control.activity.activity_report', compact('tree', 'project', 'periods', 'activities'));
    }

    function buildTree()
    {
        $previousPeriod = $this->period->project->periods()->where('id', '<', $this->period->id)->first();
        if ($previousPeriod) {
            $previousData = MasterShadow::previousActivityReport($this->period)->get()->groupBy('wbs_id')->map(function ($group) {
                return $group->keyBy('activity');
            });
        } else {
            $previousData = [];
        }

        $currentData = $this->applyFilters(MasterShadow::currentActivityReport($this->period))->get()->groupBy('wbs_id')->map(function ($group) {
            return $group->keyBy('activity');
        });

        $wbsData = MasterShadow::forPeriod($this->period)->orderBy('wbs')->pluck('wbs', 'wbs_id')->map(function ($wbs) {
            return json_decode($wbs, true);
        });


        $tree = [];
        foreach ($currentData as $wbs_id => $wbsGroup) {
            foreach ($wbsGroup as $activity => $activityCurrent) {
                $key = '';
                $activityPrevious = $previousData[$wbs_id][$activity] ?? ['prev_cost' => 0, 'prev_allowable' => 0, 'prev_cost_var' => 0];
                foreach ($wbsData[$wbs_id] as $wbsLevel) {
                    $lastKey = $key;
                    $key .= $wbsLevel;
                    if (!isset($tree[$key])) {
                        $tree[$key] = [
                            'budget_cost' => 0, 'to_date_cost' => 0, 'to_date_allowable' => 0, 'to_date_var' => 0,
                            'prev_cost' => 0, 'prev_allowable' => 0, 'prev_cost_var' => 0,
                            'remaining_cost' => 0, 'completion_cost' => 0, 'completion_var' => 0, 'activities' => []
                        ];
                    }

                    $tree[$key]['parent'] = $lastKey;
                    $tree[$key]['name'] = $wbsLevel;
                    $tree[$key]['budget_cost'] += $activityCurrent['budget_cost'];
                    $tree[$key]['to_date_cost'] += $activityCurrent['to_date_cost'];
                    $tree[$key]['to_date_allowable'] += $activityCurrent['to_date_allowable'];
                    $tree[$key]['to_date_var'] += $activityCurrent['to_date_var'];
                    $tree[$key]['prev_cost'] += $activityPrevious['prev_cost'];
                    $tree[$key]['prev_allowable'] += $activityPrevious['prev_allowable'];
                    $tree[$key]['prev_cost_var'] += $activityPrevious['prev_cost_var'];
                    $tree[$key]['remaining_cost'] += $activityCurrent['remaining_cost'];
                    $tree[$key]['completion_cost'] += $activityCurrent['completion_cost'];
                    $tree[$key]['completion_var'] += $activityCurrent['completion_var'];
                }

                $tree[$key]['activities'][$activity] = [
                    'budget_cost' => $activityCurrent['budget_cost'],
                    'to_date_cost' => $activityCurrent['to_date_cost'],
                    'to_date_allowable' => $activityCurrent['to_date_allowable'],
                    'to_date_var' => $activityCurrent['to_date_var'],
                    'prev_cost' => $activityPrevious['prev_cost'],
                    'prev_allowable' => $activityPrevious['prev_allowable'],
                    'prev_cost_var' => $activityPrevious['prev_cost_var'],
                    'remaining_cost' => $activityCurrent['remaining_cost'],
                    'completion_cost' => $activityCurrent['completion_cost'],
                    'completion_var' => $activityCurrent['completion_var'],
                ];
            }

        }

        return collect($tree);
    }

    private function applyFilters(Builder $query)
    {
        $request = request();

        if ($status = strtolower($request->get('status', ''))) {
            if ($status == 'not started') {
                $query->havingRaw('sum(to_date_qty) = 0');
            } elseif ($status == 'in progress') {
                $query->havingRaw('sum(to_date_qty) > 0 AND AVG(progress) < 100');
            } elseif ($status == 'closed') {
                $query->where('to_date_qty', '>', 0)->where('progress', 100);
            }
        }

        if ($wbs = $request->get('wbs')) {
            $term = "%$wbs%";
            $levels = WbsLevel::where('project_id', $this->project->id)->where(function ($q) use ($term) {
                $q->where('code', 'like', $term)->orWhere('name', 'like', $term);
            })->pluck('id');
            $query->whereIn('wbs_id', $levels);
        }

        if ($activity = $request->get('activity')) {
            $query->where('activity', $activity);
        }

        if ($request->exists('negative_to_date')) {
            $query->havingRaw('to_date_var < 0');
        }

        if ($request->exists('negative_completion')) {
            $query->having('completion_var', '<', 0);
        }

        return $query;
    }


}