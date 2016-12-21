<?php
/**
 * Created by PhpStorm.
 * User: omar
 * Date: 21/12/16
 * Time: 11:11 ص
 */

namespace App\Http\Controllers\Reports\CostReports;


use App\CostShadow;
use App\Project;

class CostSummery
{
    function getCostSummery(Project $project)
    {
        $data = [];
        $shadows = CostShadow::where('project_id',$project->id)->where('period_id',$project->open_period()->id)->get();

        $shadows = CostShadow::join('break_down_resource_shadows as sh','sh.breakdown_resource_id','=','cost_shadows.breakdown_resource_id')
            ->where('project_id',$project->id)
            ->where('period_id',$project->open_period())->get();
        dd($shadows);
        foreach ($shadows as $shadow){
            if(!isset($data[$shadow->budget->resource_type])){
                $data[$shadow->budget->resource_type]= [
                    'name'=>$shadow->budget->resource_type,
                    'baseline'=>0,
                    'previous_cost'=>0,
                    'previous_allowable'=>0,
                    'todate_cost'=>0,
                    'todate_variance'=>0,
                    'remaining_cost'=>0,
                    'at_completion_cost'=>0,
                    'cost_variance'=>0,
                    'actual_cost'=>0,
                    'earned_value'=>0,
                ];
            }

            $data[$shadow->budget->resource_type]['baseline']+=$shadow->budget->budget_cost;
            $data[$shadow->budget->resource_type]['previous_cost']+=$shadow->previous_cost;
            $data[$shadow->budget->resource_type]['previous_allowable']+=$shadow->todate_cost;
            $data[$shadow->budget->resource_type]['todate_cost']+=$shadow->todate_cost;
            $data[$shadow->budget->resource_type]['todate_variance']+=$shadow->todate_cost;
            $data[$shadow->budget->resource_type]['remaining_cost']+=$shadow->remaining_cost;
            $data[$shadow->budget->resource_type]['at_completion_cost']+=$shadow->completion_cost;
            $data[$shadow->budget->resource_type]['cost_variance']+=$shadow->cost_var;
            $data[$shadow->budget->resource_type]['earned_value']+=$shadow->allowable_ev_cost;
        }
        return view('reports.cost-control.cost_summery',compact('data'));
    }
}