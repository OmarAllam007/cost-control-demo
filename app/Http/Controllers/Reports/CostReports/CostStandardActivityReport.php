<?php
/**
 * Created by PhpStorm.
 * User: omar
 * Date: 22/12/16
 * Time: 02:17 م
 */

namespace App\Http\Controllers\Reports\CostReports;


use App\ActivityDivision;
use App\BreakDownResourceShadow;
use App\CostShadow;
use App\Http\Controllers\Reports\Productivity;
use App\Project;
use App\StdActivity;

class CostStandardActivityReport
{

    function getStandardActivities(Project $project)
    {

        $shadows = CostShadow::joinBudget('budget.activity_id')
            ->sumFields([
                'cost.previous_cost'
                , 'cost.to_date_cost'
                , 'cost.allowable_ev_cost'
                , 'cost.allowable_var'
                , 'cost.completion_cost'
                , 'cost.cost_var'
                , 'cost.remaining_cost',
            ])->where('budget.project_id', $project->id)
            ->where('period_id', $project->open_period()->id)
            ->get();

        $budgets = BreakDownResourceShadow::where('project_id', $project->id)->get();

        $prevShadows = CostShadow::where('period_id','<',$project->open_period()->id)
            ->where('project_id',$project->id)->get();

        $data = [];

        foreach ($shadows as $shadow) {
            $activity = StdActivity::find($shadow['activity_id']);
            $division = $activity->division;
            if (!isset($data[$division->name])) {
                $data[$division->name] = ['division_id' => $division->id, 'name' => $division->name, 'activities' => [],];
            }
            if (!isset($data[$division->name]['activities'][$activity->name])) {
                $data[$division->name]['activities'][$activity->name] =
                    ['activity_id' => $activity->id, 'name' => $activity->name, 'budget_cost' => 0, 'previous_date_cost' => $shadow['previous_cost'] ?: 0, 'previous_allowable_ev_cost' => 0, 'previous_to_date_var' => 0, 'to_date_cost' => $shadow['to_date_cost'], 'to_date_allowable_ev_cost' => $shadow['allowable_ev_cost'], 'to_date_var' => $shadow['allowable_var'], 'remain_cost' => $shadow['remaining_cost'], 'at_completion_cost' => $shadow['completion_cost'], 'cost_variance' => $shadow['cost_var']];
            }
        }
        //get budget cost of activities
        foreach ($budgets as $budget) {
            if (isset($data[$budget->std_activity->division->name]['activities'][$budget['activity']])) {
                $data[$budget->std_activity->division->name]['activities'][$budget->std_activity->name]['budget_cost'] += $budget->budget_cost;
            }

        }
        // get previous
        foreach ($prevShadows as $prevShadow){
            $activity = StdActivity::find($shadow['activity_id']);
            $division = $activity->division;
            if(isset($data[$division->name]['activities'][$activity->name])){
                $data[$division->name]['activities'][$activity->name]['previous_allowable_ev_cost']+=$shadow['allowable_ev_cost'];
                $data[$division->name]['activities'][$activity->name]['previous_cost_var']+=$shadow['cost_var'];
            }
        }

        return view('cost-control.cost_standard_activity',compact('data'));

    }


}