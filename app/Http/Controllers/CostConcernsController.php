<?php

namespace App\Http\Controllers;

use App\CostConcerns;
use App\CostResource;
use App\Project;
use Illuminate\Http\Request;

use App\Http\Requests;

class CostConcernsController extends Controller
{
    function addConcernCostReport($project, Request $request)
    {
        $info = $request->info;
        $report_name = $request->report_name;
        $comment = $request->comment;
        $project_id = $project;
        $period_id = Project::find($project_id)->getMaxPeriod();

         CostConcerns::create(['project_id' => $project_id, 'period_id' => $period_id
            , 'comment' => $comment, 'data' => $info, 'report_name' => $report_name
        ]);
    }
}
