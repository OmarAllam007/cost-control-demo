<?php
/**
 * Created by PhpStorm.
 * User: omar
 * Date: 28/12/16
 * Time: 02:07 م
 */

namespace App\Http\Controllers\Reports\CostReports;


use App\Breakdown;
use App\BreakDownResourceShadow;
use App\CostShadow;
use App\Project;
use App\WbsLevel;

class ActivityReport
{
    protected $project;
    public $root_ids = [];

    function getActivityReport(Project $project)
    {
        $this->project = $project;
        $tree = [];
        $levels = CostShadow::all()->pluck('wbs_level_id')->unique()->toArray();
        $wbs_levels = WbsLevel::whereIn('id', $levels)->where('project_id', $project->id)->get();
        foreach ($wbs_levels as $level) {
            if ($level->root) {
                $treeLevel = $this->buildTree($level->root);
            }
            if($treeLevel){
                $tree[] = $treeLevel;
            }
        }
        return view('reports.cost-control.activity.activity_report', compact('tree','project'));
    }

    function buildTree($level)
    {
        $tree=[];
        if (!in_array($level->id, $this->root_ids)) {
            $this->root_ids[] = $level->id;
            $tree = ['id' => $level->id, 'name' => $level->name, 'children' => [], 'activities' => [], 'data' => [
                'to_date_cost' => 0,
                'previous_cost' => 0,
                'allowable_ev_cost' => 0,
                'remaining_cost' => 0,
                'completion_cost' => 0,
                'cost_var' => 0,
                'allowable_var' => 0,
                'budget_cost' => 0,
            ]];

            /** @var WbsLevel $level */
            $tree['activities'] = $this->activityArray($level);

            $budget_cost = BreakDownResourceShadow::where('project_id', $this->project->id)
                ->where('activity_id', $tree['activities']['activity_id'])->get()->sum('budget_cost');

            $shadows = CostShadow::joinBudget('budget.activity')
                ->sumFields(
                    [
                        'cost.to_date_cost', 'cost.previous_cost',
                        'cost.allowable_ev_cost', 'cost.remaining_cost', 'cost.completion_cost',
                        'cost.cost_var', 'cost.allowable_var',
                    ]
                )->where('budget.project_id', $this->project->id)
                ->whereIn('wbs_id', $level->getChildrenIds())->get()->toArray();

            foreach ($shadows as $shadow) {
                $tree['activities']['budget_cost'] += $budget_cost;
                $tree['activities']['to_date_cost'] += $shadow['to_date_cost'];
                $tree['activities']['previous_cost'] += $shadow['previous_cost'];
                $tree['activities']['allowable_ev_cost'] += $shadow['allowable_ev_cost'];
                $tree['activities']['remaining_cost'] += $shadow['remaining_cost'];
                $tree['activities']['completion_cost'] += $shadow['completion_cost'];
                $tree['activities']['cost_var'] += $shadow['cost_var'];
                $tree['activities']['allowable_var'] += $shadow['allowable_var'];
            }


            if ($level->children->count()) {
                $tree['children'] = $level->children->map(function (WbsLevel $childLevel) {
                    return $this->buildTree($childLevel);
                });
            }

        }
        return $tree;
    }

    function activityArray($level)
    {
        //fill activities with it's data
        $activities = [
            'activity_id' => 0,
            'to_date_cost' => 0,
            'previous_cost' => 0,
            'allowable_ev_cost' => 0,
            'remaining_cost' => 0,
            'completion_cost' => 0,
            'cost_var' => 0,
            'allowable_var' => 0,
            'budget_cost' => 0,
        ];

        $shadows = CostShadow::joinBudget('budget.activity_id')
            ->sumFields(
                [
                    'cost.to_date_cost', 'cost.previous_cost',
                    'cost.allowable_ev_cost', 'cost.remaining_cost', 'cost.completion_cost',
                    'cost.cost_var', 'cost.allowable_var',
                ]
            )->where('cost.project_id', $this->project->id)
            ->where('wbs_id', $level->id)->get()->toArray();

        foreach ($shadows as $shadow) {
            $activities['activity_id'] = $shadow['activity_id'] ?: 0;
            $activities['to_date_cost'] = $shadow['to_date_cost'] ?: 0;
            $activities['previous_cost'] = $shadow['previous_cost'] ?: 0;
            $activities['allowable_ev_cost'] = $shadow['allowable_ev_cost'] ?: 0;
            $activities['remaining_cost'] = $shadow['remaining_cost'] ?: 0;
            $activities['completion_cost'] = $shadow['completion_cost'] ?: 0;
            $activities['cost_var'] = $shadow['cost_var'] ?: 0;
            $activities['allowable_var'] = $shadow['allowable_var'] ?: 0;
            $activities['budget_cost'] = BreakDownResourceShadow::where('project_id', $this->project->id)
                ->where('activity_id', $shadow['activity_id'])->get()->sum('budget_cost');
        }

        return $activities;
    }
}