<?php
/**
 * Created by PhpStorm.
 * User: omar
 * Date: 20/12/16
 * Time: 03:45 م
 */

namespace App\Http\Controllers\Reports\CostReports;


use App\CostShadow;
use App\Project;

class ProjectInformation
{
    function getProjectInformation(Project $project)
    {

        //convert project duration to dayes and plus it to start date


        $data=[];
        $shadows = CostShadow::where('project_id',$project->id)->groupBy('period_id')->select('period_id')->selectRaw('sum(to_date_cost) as todate_cost')->selectRaw('sum(allowable_ev_cost) as 
        allowable_cost')
        ->get();
        foreach ($shadows as $shadow) {
            if(!isset($data[$shadow->period_id])){
                $data[$shadow->period_id] = [
                    'actual_cost'=>0,
                    'allowable_cost'=>0,
                    'cpi'=>0,
                    'cost_variance'=>0,
                ];
                $data[$shadow->period_id]['actual_cost']+=$shadow->todate_cost;
                $data[$shadow->period_id]['allowable_cost']+=$shadow->allowable_cost;
            }

        }

        return view('reports.cost-control.project_information', compact('project','data', 'allowable_cost', 'actual_cost'));

    }

}