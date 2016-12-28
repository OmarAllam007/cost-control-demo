<?php
/**
 * Created by PhpStorm.
 * User: omar
 * Date: 21/12/16
 * Time: 11:11 ص
 */

namespace App\Http\Controllers\Reports\CostReports;


use App\BreakdownResource;
use App\BreakDownResourceShadow;
use App\CostShadow;
use App\Project;

class CostSummery
{
    function getCostSummery(Project $project)
    {
        //get current from open period
        //get previous from prev period
        // get remaining

        $shadows = CostShadow::joinBudget('budget.resource_type')
            ->sumFields([
                'cost.to_date_cost',
                'cost.previous_cost',
                'cost.allowable_ev_cost',
                'cost.remaining_cost',
                'cost.completion_cost',
                'cost.cost_var'])
            ->where('period_id', $project->open_period()->id)
            ->get();

        $budgets = BreakDownResourceShadow::sumFields('resource_type', ['budget_cost'])->where('project_id', $project->id)->get();
        dd($budgets);
        $previousShadows = CostShadow::where('period_id', '<', $project->open_period()->id)->where('project_id', $project->id)->get();

        $data = [];
        foreach ($budgets as $budget) {
            if (!isset($data[$budget['resource_type']])) {
                $data[$budget['resource_type']] = [
                    'budget_cost' => 0,
                ];
                $data[$budget['resource_type']]['budget_cost'] += $budget['budget_cost'];
            }

        }
        foreach ($shadows as $shadow) {
            if (isset($data[$shadow['resource_type']])) {
                $data[$shadow['resource_type']] = [
                    'budget_cost' => $data[$shadow['resource_type']]['budget_cost'],
                    'to_date_cost' => $shadow['to_date_cost'],
                    'previous_cost' => $shadow['previous_cost'],
                    'previous_allowable' => 0,
                    'previous_variance' => 0,
                    'allowable_ev_cost' => $shadow['allowable_ev_cost'],
                    'cost_var' => $shadow['cost_var'],
                    'remaining_cost' => $shadow['remaining_cost'],
                    'completion_cost' => $shadow['completion_cost'],

                ];
            }
        }
        if ($previousShadows) {
            foreach ($previousShadows as $previousShadow) {
                if (isset($data[$previousShadow['resource_type']])) {
                    $data[$previousShadow['resource_type']]['previous_allowable'] += $previousShadow['allowable_ev_cost'];
                    $data[$previousShadow['resource_type']]['previous_variance'] += $previousShadow['cost_var'];

                }
            }
        }
        dd($data);
        return view('reports.cost-control.cost_summery', compact('data'));
    }
}