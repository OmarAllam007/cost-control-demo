<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Reports\CostReports\ActivityReport;
use App\Http\Controllers\Reports\CostReports\BoqReport;
use App\Http\Controllers\Reports\CostReports\CostStandardActivityReport;
use App\Http\Controllers\Reports\CostReports\CostSummery;
use App\Http\Controllers\Reports\CostReports\OverdraftReport;
use App\Http\Controllers\Reports\CostReports\ProductivityReport;
use App\Http\Controllers\Reports\CostReports\ProjectInformation;
use App\Http\Controllers\Reports\CostReports\ResourceCodeReport;
use App\Http\Controllers\Reports\CostReports\ResourceDictionaryReport;
use App\Http\Controllers\Reports\CostReports\SignificantMaterials;
use App\Http\Controllers\Reports\CostReports\StandardActivity;
use App\Http\Controllers\Reports\CostReports\VarianceAnalysisReport;
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

    public function costSummery(Project $project, Request $request)
    {
        if ($request->period_id) {
            if (\Session::has('period_id'.$project->id)) {
                \Session::forget('period_id'.$project->id);
                \Session::set('period_id'.$project->id, $request->period_id);
                $chosen_period_id = $request->period_id;
            } else {
                $chosen_period_id = $project->getMaxPeriod();
                \Session::set('period_id'.$project->id, $request->period_id);
            }
        }
        else{
            if (\Session::has('period_id'.$project->id)) {
                $chosen_period_id = \Session::get('period_id'.$project->id);;
            } else {
                $chosen_period_id = $project->getMaxPeriod();
                \Session::set('period_id'.$project->id, $request->period_id);
            }
        }

        $cost_summery = new CostSummery();
        return $cost_summery->getCostSummery($project, $chosen_period_id);
    }

    public function significantMaterials(Project $project, Request $request)
    {
        if ($request->period_id) {
            if (\Session::has('period_id'.$project->id.$project->id)) {
                \Session::forget('period_id'.$project->id);
                \Session::set('period_id'.$project->id, $request->period_id);
                $chosen_period_id = $request->period_id;
            } else {
                $chosen_period_id = $project->getMaxPeriod();
                \Session::set('period_id'.$project->id, $request->period_id);
            }
        }
        else{
            if (\Session::has('period_id'.$project->id)) {
                $chosen_period_id = \Session::get('period_id'.$project->id);;
            } else {
                $chosen_period_id = $project->getMaxPeriod();
                \Session::set('period_id'.$project->id, $request->period_id);
            }
        }
        $importantMaterials = new SignificantMaterials();
        return $importantMaterials->getTopHighPriorityMaterials($project,$chosen_period_id);
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

    public function resourceCodeReport(Project $project)
    {
        $code = new ResourceCodeReport();
        return $code->getResourceCodeReport($project);
    }

    public function overdraftReport(Project $project)
    {
        $draft = new OverdraftReport();
        return $draft->getDraft($project);
    }

    public function activityReport(Project $project)
    {
        $activity = new ActivityReport();
        return $activity->getActivityReport($project);
    }

    public function resourceDictionaryReport(Project $project)
    {
        $dictionary = new ResourceDictionaryReport();
        return $dictionary->getReport($project);
    }

    public function productivityReport(Project $project)
    {
        $productivity = new ProductivityReport();
        return $productivity->getCostProductivity($project);
    }

    public function varianceAnalysisReport(Project $project)
    {
        $variance = new VarianceAnalysisReport();
        return $variance->getVarianceReport($project);
    }
}
