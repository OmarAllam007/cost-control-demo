<?php

namespace App\Http\Controllers;

use App\CostConcerns;
use App\CostResource;
use App\Project;
use Illuminate\Http\Request;

use App\Http\Requests;

class CostConcernsController extends Controller
{
    protected $project;
    function addConcernReport($project, Request $request)
    {
        $this->project = $project;
        $info = $request->info;
        $report_name = $request->report_name;
        $comment = $request->comment;
        $project_id = $project;
        $period_id = Project::find($project_id)->getMaxPeriod();

        CostConcerns::create(['project_id' => $project_id, 'period_id' => $period_id
            , 'comment' => $comment, 'data' => $info, 'report_name' => $report_name
        ]);
    }

    function getConcernReport($project,$report_name)
    {
        $concerns_data = [];
        if ($report_name == 'Cost Summary Report') {
            $concerns = CostConcerns::where('report_name', $report_name)->where('project_id', $project->id)->get();
            foreach ($concerns as $concern) {
                $name = json_decode($concern->data);
                if (!isset($concerns_data[$name->name]['comments'][$concern->comment])) {
                    $concerns_data[$name->name]['comments'][$concern->comment] = $concern->comment;
                }

            }
        }
        if ($report_name == 'Standard Activity') {
            $concerns = CostConcerns::where('report_name', $report_name)->where('project_id', $project->id)->get();
            foreach ($concerns as $concern) {
                $name = json_decode($concern->data);
                if (!isset($concerns_data[$name->name]['comments'][$concern->comment])) {
                    $concerns_data[$name->name]['comments'][$concern->comment] = $concern->comment;
                }

            }
        }

        return $concerns_data;
    }
}