<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Reports\CostReports\ActivityReport;
use App\Http\Controllers\Reports\CostReports\BoqReport;
use App\Http\Controllers\Reports\CostReports\CostStandardActivityReport;
use App\Http\Controllers\Reports\CostReports\CostSummery;
use App\Http\Controllers\Reports\CostReports\OverdraftReport;
use App\Http\Controllers\Reports\CostReports\ProjectInformation;
use App\Http\Controllers\Reports\CostReports\ResourceCodeReport;
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

    public function standardActivity(Project $project)
    {

        $standard_activity = new CostStandardActivityReport();
        return $standard_activity->getStandardActivities($project);
    }

    public function boqReport(Project $project)
    {
        $boq = new BoqReport();
        return $boq->getReport($project);
    }

    public function resourceCodeReport(Project $project){
        $code = new ResourceCodeReport();
        return $code->getResourceCodeReport($project);
    }

    public function overdraftReport(Project $project){
        $draft = new OverdraftReport();
        return $draft->getDraft($project);
    }

    public function activityReport(Project $project){
        $activity = new ActivityReport();
        $activity->getActivityReport($project);
    }
}
