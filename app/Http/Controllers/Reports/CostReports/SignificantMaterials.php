<?php
/**
 * Created by PhpStorm.
 * User: omar
 * Date: 21/12/16
 * Time: 10:05 م
 */

namespace App\Http\Controllers\Reports\CostReports;


use App\BreakDownResourceShadow;
use App\CostShadow;
use App\Project;

class SignificantMaterials
{
    function getSignifcantMaterials(Project $project)
    {
        $data = [];
        $budgets = BreakDownResourceShadow::OrderBy('budget_cost', 'DESC')->where('project_id', $project->id)->get();
        $costShadows = CostShadow::where('project_id', $project->id)->where('period_id', $project->open_period()->id)->get();
        $prevShadows = CostShadow::where('project_id',$project->id)->where('period_id','<',$project->open_period()->id)->get();
        /** get budget for materials resources */
        foreach ($budgets as $item) {

            if ($item['resource_type'] == '03.MATERIAL') {
                if (!isset($data[$item['resource_name']])) {
                    $data[$item['resource_name']] = [
                        'budget_cost' => 0,
                    ];
                    $data[$item['resource_name']]['budget_cost'] += $item['budget_cost'];
                }

            }
        }

        /* get costshadow materials resources */
        foreach ($costShadows as $shadow) {
            $resourceName = $shadow->budget->resource->name;
            if (isset($data[$resourceName])) {
                $data[$resourceName] = [
                    'budget_cost' => $data[$resourceName]['budget_cost'],
                    'previous_cost'=>0,
                    'previous_allowable'=>0,
                    'previous_variance'=>0,
                    'to_date_cost'=>0,
                    'allowable_ev_cost'=>0,
                    'to_date_variance'=>0,
                    'remaining_cost'=>0,
                    'at_completion_cost'=>0,
                    'cost_variance'=>0,
                ];
                $data[$resourceName]['to_date_cost'] += $shadow['to_date_cost'];
                $data[$resourceName]['previous_cost'] += $shadow['previous_cost'];
                $data[$resourceName]['allowable_ev_cost'] += $shadow['allowable_ev_cost'];
                $data[$resourceName]['to_date_variance'] += $shadow['allowable_var'];
                $data[$resourceName]['remaining_cost'] += $shadow['remaining_cost'];
                $data[$resourceName]['at_completion_cost'] += $shadow['completion_cost'];
                $data[$resourceName]['cost_variance'] += $shadow['cost_variance'];
            }
        }

        /* get prev period materials*/
        foreach ($prevShadows as $prevShadow) {
            $resourceName = $prevShadow->budget->resource->name;
            if (isset($data[$resourceName])) {
                $data[$resourceName] = [
                    'budget_cost' => $data[$resourceName]['budget_cost'],
                    'to_date_cost'=>$data[$resourceName]['to_date_cost'],
                    'allowable_ev_cost'=>$data[$resourceName]['allowable_ev_cost'],
                    'to_date_variance'=>$data[$resourceName]['to_date_variance'],
                    'remaining_cost'=>$data[$resourceName]['remaining_cost'],
                    'at_completion_cost'=>$data[$resourceName]['at_completion_cost'],
                    'cost_variance'=>$data[$resourceName]['cost_variance'],
                ];
                $data[$resourceName]['previous_allowable'] += $prevShadow['allowable_ev_cost'];
                $data[$resourceName]['previous_variance'] += $prevShadow['to_date_cost']-$prevShadow['allowable_ev_cost'];
            }
        }

        //get first twenty
        $data=array_slice($data,0,20);

        return view('reports.cost-control.significant_materials',compact('data'));
    }

}