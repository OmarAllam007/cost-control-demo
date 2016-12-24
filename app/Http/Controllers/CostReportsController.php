<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Reports\CostReports\CostStandardActivityReport;
use App\Http\Controllers\Reports\CostReports\CostSummery;
use App\Http\Controllers\Reports\CostReports\ProjectInformation;
use App\Http\Controllers\Reports\CostReports\SignificantMaterials;
use App\Http\Controllers\Reports\CostReports\StandardActivity;
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

    public function costSummery(Project $project)
    {
        $cost_summery = new CostSummery();
        return $cost_summery->getCostSummery($project);
    }

    public function significantMaterials(Project $project)
    {
        $importantMaterials = new SignificantMaterials();
        return $importantMaterials->getSignifcantMaterials($project);
    }

    public function standardActivity(Project $project){

        $standard_activity = new CostStandardActivityReport();
        return $standard_activity->getStandardActivities($project);
    }
}
