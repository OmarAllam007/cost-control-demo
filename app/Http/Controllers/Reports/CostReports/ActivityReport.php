<?php

namespace App\Http\Controllers\Reports\CostReports;

use App\MasterShadow;
use App\Period;
use App\Project;

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

        return view('reports.cost-control.activity.activity_report', compact('tree', 'project'));
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

        $currentData = MasterShadow::currentActivityReport($this->period)->get()->groupBy('wbs_id')->map(function ($group) {
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
                            'remaining_cost' => 0, 'completion_cost' => 0, 'completion_var' => 0
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

                $wbs[$key]['activities'][$activity] = [
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


}