<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Reports\CostReports\CostSummery;
use App\Http\Controllers\Reports\CostReports\ProjectInformation;
use App\Project;
use Illuminate\Http\Request;

use App\Http\Requests;

class CostReportsController extends Controller
{

    public function projectInformation(Project $project)
    {
        $projectInfo = new ProjectInformation();
        return $projectInfo->getProjectInformation($project);
    }

    public function costSummery(Project $project){
        $cost_summery = new CostSummery();
        return $cost_summery->getCostSummery($project);
    }
}
