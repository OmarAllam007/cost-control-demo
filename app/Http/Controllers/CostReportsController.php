<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Reports\CostReports\ActivityReport;
use App\Http\Controllers\Reports\CostReports\BoqReport;
use App\Http\Controllers\Reports\CostReports\CostStandardActivityReport;
use App\Http\Controllers\Reports\CostReports\CostSummery;
use App\Http\Controllers\Reports\CostReports\IssuesReport;
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

    public function projectInformation(Project $project, Request $request)
    {
        if ($request->period_id) {
            if (\Session::has('period_id' . $project->id)) {
                \Session::forget('period_id' . $project->id);
                \Session::set('period_id' . $project->id, $request->period_id);
                $chosen_period_id = $request->period_id;
            } else {
                $chosen_period_id = $project->getMaxPeriod();
                \Session::set('period_id' . $project->id, $request->period_id);
            }
        } else {
            if (\Session::has('period_id' . $project->id)) {
                $chosen_period_id = \Session::get('period_id' . $project->id);;
            } else {
                $chosen_period_id = $project->getMaxPeriod();
                \Session::set('period_id' . $project->id, $request->period_id);
            }
        }
        $projectInfo = new ProjectInformation();
        return $projectInfo->getProjectInformation($project, $chosen_period_id);
    }

    public function costSummery(Project $project, Request $request)
    {
        if ($request->period_id) {
            if (\Session::has('period_id' . $project->id)) {
                \Session::forget('period_id' . $project->id);
                \Session::set('period_id' . $project->id, $request->period_id);
                $chosen_period_id = $request->period_id;
            } else {
                $chosen_period_id = $project->getMaxPeriod();
                \Session::set('period_id' . $project->id, $request->period_id);
            }
        } else {
            if (\Session::has('period_id' . $project->id)) {
                $chosen_period_id = \Session::get('period_id' . $project->id);;
            } else {
                $chosen_period_id = $project->getMaxPeriod();
                \Session::set('period_id' . $project->id, $request->period_id);
            }
        }

        $cost_summery = new CostSummery();
        return $cost_summery->getCostSummery($project, $chosen_period_id);
    }

    public function significantMaterials(Project $project, Request $request)
    {
        if ($request->period_id) {
            if (\Session::has('period_id' . $project->id . $project->id)) {
                \Session::forget('period_id' . $project->id);
                \Session::set('period_id' . $project->id, $request->period_id);
                $chosen_period_id = $request->period_id;
            } else {
                $chosen_period_id = $project->getMaxPeriod();
                \Session::set('period_id' . $project->id, $request->period_id);
            }
        } else {
            if (\Session::has('period_id' . $project->id)) {
                $chosen_period_id = \Session::get('period_id' . $project->id);;
            } else {
                $chosen_period_id = $project->getMaxPeriod();
                \Session::set('period_id' . $project->id, $request->period_id);
            }
        }
        $importantMaterials = new SignificantMaterials();
        return $importantMaterials->getTopHighPriorityMaterials($project, $chosen_period_id);
    }

    public function standardActivity(Project $project, Request $request)
    {
        $chosen_period_id = $this->getPeriod($project, $request);

        $standard_activity = new CostStandardActivityReport();
        return $standard_activity->getStandardActivities($project, $chosen_period_id);
    }

    public function boqReport(Project $project, Request $request)
    {
        if ($request->period_id) {
            if (\Session::has('period_id' . $project->id)) {
                \Session::forget('period_id' . $project->id);
                \Session::set('period_id' . $project->id, $request->period_id);
                $chosen_period_id = $request->period_id;
            } else {
                $chosen_period_id = $project->getMaxPeriod();
                \Session::set('period_id' . $project->id, $request->period_id);
            }
        } else {
            if (\Session::has('period_id' . $project->id)) {
                $chosen_period_id = \Session::get('period_id' . $project->id);;
            } else {
                $chosen_period_id = $project->getMaxPeriod();
                \Session::set('period_id' . $project->id, $request->period_id);
            }
        }

        $boq = new BoqReport();
        return $boq->getReport($project, $chosen_period_id);
    }

    public function resourceCodeReport(Project $project, Request $request)
    {
        $period = $this->getPeriod($project, $request);
        $resourceCodeReport = new ResourceCodeReport($project, $period);
        return $resourceCodeReport->run();
    }

    public function overdraftReport(Project $project, Request $request)
    {
        if ($request->period_id) {
            if (\Session::has('period_id' . $project->id)) {
                \Session::forget('period_id' . $project->id);
                \Session::set('period_id' . $project->id, $request->period_id);
                $chosen_period_id = $request->period_id;
            } else {
                $chosen_period_id = $project->getMaxPeriod();
                \Session::set('period_id' . $project->id, $request->period_id);
            }
        } else {
            if (\Session::has('period_id' . $project->id)) {
                $chosen_period_id = \Session::get('period_id' . $project->id);;
            } else {
                $chosen_period_id = $project->getMaxPeriod();
                \Session::set('period_id' . $project->id, $request->period_id);
            }
        }
        $draft = new OverdraftReport();
        return $draft->getDraft($project, $chosen_period_id);
    }

    public function activityReport(Project $project, Request $request)
    {
        if ($request->period_id) {
            if (\Session::has('period_id' . $project->id)) {
                \Session::forget('period_id' . $project->id);
                \Session::set('period_id' . $project->id, $request->period_id);
                $chosen_period_id = $request->period_id;
            } else {
                $chosen_period_id = $project->getMaxPeriod();
                \Session::set('period_id' . $project->id, $request->period_id);
            }
        } else {
            if (\Session::has('period_id' . $project->id)) {
                $chosen_period_id = \Session::get('period_id' . $project->id);;
            } else {
                $chosen_period_id = $project->getMaxPeriod();
                \Session::set('period_id' . $project->id, $request->period_id);
            }
        }
        $activity = new ActivityReport();
        return $activity->getActivityReport($project, $chosen_period_id);
    }

    public function resourceDictionaryReport(Project $project)
    {
        $dictionary = new ResourceDictionaryReport();
        return $dictionary->getReport($project);
    }

    public function productivityReport(Project $project)
    {
        $file_ex = \File::exists(public_path() . '/files/productivity/Productivity-' . $project->id . '-period-' . $project->getMaxPeriod() . '.xlsx');
        $headers = array('Content-Type: application/xlsx');
        return \Response::download(public_path() . '/files/productivity/Productivity-' . $project->id . '-period-' . $project->getMaxPeriod() . '.xlsx', 'report.xlsx', $headers);

//        return view('reports.cost-control.productivity.productivity');
    }

    public function varianceAnalysisReport(Project $project, Request $request)
    {
        if ($request->period_id) {
            if (\Session::has('period_id' . $project->id)) {
                \Session::forget('period_id' . $project->id);
                \Session::set('period_id' . $project->id, $request->period_id);
                $chosen_period_id = $request->period_id;
            } else {
                $chosen_period_id = $project->getMaxPeriod();
                \Session::set('period_id' . $project->id, $request->period_id);
            }
        } else {
            if (\Session::has('period_id' . $project->id)) {
                $chosen_period_id = \Session::get('period_id' . $project->id);;
            } else {
                $chosen_period_id = $project->getMaxPeriod();
                \Session::set('period_id' . $project->id, $request->period_id);
            }
        }
        $variance = new VarianceAnalysisReport();
        return $variance->getVarianceReport($project, $chosen_period_id);
    }

    function issuesReport(Project $project, Request $request)
    {
        if ($request->period_id) {
            if (\Session::has('period_id' . $project->id)) {
                \Session::forget('period_id' . $project->id);
                \Session::set('period_id' . $project->id, $request->period_id);
                $chosen_period_id = $request->period_id;
            } else {
                $chosen_period_id = $project->getMaxPeriod();
                \Session::set('period_id' . $project->id, $request->period_id);
            }
        } else {
            if (\Session::has('period_id' . $project->id)) {
                $chosen_period_id = \Session::get('period_id' . $project->id);;
            } else {
                $chosen_period_id = $project->getMaxPeriod();
                \Session::set('period_id' . $project->id, $request->period_id);
            }
        }
        $variance = new IssuesReport();
        return $variance->getIssuesReport($project, $chosen_period_id);
    }

    protected function getPeriod(Project $project, Request $request)
    {
        if ($request->period) {
            \Session::set('period_id_' . $project->id, $request->period);
        } elseif (!$request->session()->get('period_id_' . $project->id)) {
            \Session::set('period_id_' . $project->id, $project->getMaxPeriod());
        }

        $chosen_period_id = \Session::get('period_id_' . $project->id);
        return intval($chosen_period_id);
    }


}
