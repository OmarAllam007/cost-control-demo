<?php
/**
 * Created by PhpStorm.
 * User: omar
 * Date: 3/1/17
 * Time: 2:43 PM
 */

namespace App\Http\Controllers\Reports\CostReports;


use App\CostConcerns;

class IssuesReport
{
    function getIssuesReport($project, $period_id)
    {
        $concerns = CostConcerns::where('project_id',$project->id)->where('period_id',$period_id)->get();
        $data = [];
        foreach ($concerns as $concern){
            $data_json = json_decode($concern->data);
            $data[$concern->report_name]=['data'=>[]];

        }
        dd($data);
    }

}