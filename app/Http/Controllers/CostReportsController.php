<?php

namespace App\Http\Controllers;

use App\Boq;
use App\BreakDownResourceShadow;
use App\Reports\Cost\ActivityReport;
use App\Http\Controllers\Reports\CostReports\BoqReport;
use App\Reports\Cost\CostStandardActivityReport;
use App\Reports\Cost\CostSummary;
use App\Http\Controllers\Reports\CostReports\IssuesReport;
use App\Http\Controllers\Reports\CostReports\OverdraftReport;
use App\Http\Controllers\Reports\CostReports\ResourceCodeReport;
use App\Http\Controllers\Reports\CostReports\ResourceDictionaryReport;
use App\Http\Controllers\Reports\CostReports\SignificantMaterials;
use App\Http\Controllers\Reports\CostReports\VarianceAnalysisReport;
use App\MasterShadow;
use App\Period;
use App\Project;
use App\Reports\Cost\ProductivityIndexReport;
use App\Reports\Cost\ProjectInfo;
use App\Reports\Cost\ThresholdReport;
use App\Reports\Cost\WasteIndexReport;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class CostReportsController extends Controller
{

    public function projectInformation(Project $project, Request $request)
    {
        $period_id = $this->getPeriod($project, $request);

        $report = new ProjectInfo(Period::find($period_id));

        if ($request->exists('excel')) {
            return $report->excel();
        }

        return view('reports.cost-control.project-info.index', $report->run());
    }

    public function costSummary(Project $project, Request $request)
    {
        $period_id = $this->getPeriod($project, $request);
        $period = $project->periods()->find($period_id);
        $report = new CostSummary($period);

        if ($request->exists('excel')) {
            return $report->excel();
        }

        $data = $report->run();

        return view('reports.cost-control.cost-summary.index', $data);
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
        $period = $project->periods()->find($chosen_period_id);

        $report = new CostStandardActivityReport($period);

        if ($request->exists('excel')) {
            return $report->excel();
//            $filename = view('reports.cost-control.standard_activity.excel', $data)->render();
//            return response()->download($filename, slug($project->name) . '-std-activity.xlsx');
        }

        $data = $report->run();
        return view('reports.cost-control.standard_activity.index', $data);
    }

    public function boqReport(Project $project, Request $request)
    {
        $period_id = $this->getPeriod($project, $request);
        $period = $project->periods()->find($period_id);

        $boq = new BoqReport($period);

        if ($request->exists('excel')) {
            return $boq->excel();
//            $filename = view('reports.cost-control.boq-report.excel', $data)->render();
//            return response()->download($filename, slug($project->name) . '-boq.xlsx');
        }

        $data = $boq->run();
        return view('reports.cost-control.boq-report.index', $data);
    }

    public function resourceCodeReport(Project $project, Request $request)
    {
        $period_id = $this->getPeriod($project, $request);
        $period = $project->periods()->find($period_id);

        $resourceCodeReport = new ResourceCodeReport($project, $period);


        if ($request->exists('excel')) {
            return $resourceCodeReport->excel();
        }

        $data = $resourceCodeReport->run();
        return view('reports.cost-control.resource_code.index', $data);
    }

    public function overdraftReport(Project $project, Request $request)
    {
        $period_id = $this->getPeriod($project, $request);
        $report = new OverdraftReport(Period::find($period_id));

        if ($request->exists('excel')) {
            return $report->excel();
        }

        return view('reports.cost-control.over-draft.over_draft', $report->run());
    }

    public function activityReport(Project $project, Request $request)
    {
        $period = Period::find($this->getPeriod($project, $request));
        $report = new ActivityReport($period);

        if ($request->exists('excel')) {
            return $report->excel();

//            $filename = view('reports.cost-control.activity.excel', $data)->render();
//            return response()->download($filename, slug($project->name) . '-activity.xlsx');
        }

        $data = $report->run();
        return view('reports.cost-control.activity.index', $data);
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
        $period_id = $this->getPeriod($project, $request);
        $period = Period::find($period_id);

        $report = new VarianceAnalysisReport($period);

        if ($request->exists('excel')) {
            return $report->excel();
        }

        $data = $report->run();
        return view('reports.cost-control.variance_analysis.index', $data);
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

    protected function getPeriod(Project $project, Request $request) : int
    {
        if ($request->period) {
            \Session::set('period_id_' . $project->id, $request->period);
        } elseif (!$request->session()->get('period_id_' . $project->id)) {
            \Session::set('period_id_' . $project->id, $project->getMaxPeriod());
        }

        $chosen_period_id = \Session::get('period_id_' . $project->id);
        return intval($chosen_period_id);
    }

    public function  dashboard(Project $project)
    {
        $activities = BreakDownResourceShadow::whereProjectId($project->id)
            ->selectRaw('distinct activity as name, activity_id as id')->orderBy('activity')->get();

        $resourceTypes = BreakDownResourceShadow::whereProjectId($project->id)
            ->selectRaw('distinct trim(resource_type) as name')->orderByRaw('trim(resource_type)')->get();

        $resources = BreakDownResourceShadow::whereProjectId($project->id)
            ->selectRaw('distinct resource_id id, resource_name name, resource_code code')->orderBy('name')->get();

        $boqs = Boq::with('wbs')->whereProjectId($project->id)->get()->map(function ($boq) {
            return ['id' => $boq->id, 'wbs_code' => $boq->wbs->code, 'description' => $boq->description, 'cost_account' => $boq->cost_account];
        });

        $periods = $project->periods()->readyForReporting()->pluck('name', 'id');

        return view('reports.cost-control.dashboard.dashboard', compact('project', 'activities', 'periods', 'resourceTypes', 'resources', 'boqs'));
    }

    function threshold(Project $project, Request $request)
    {
        $period = Period::find($this->getPeriod($project, $request));
        $report = new ThresholdReport($period);

        if ($request->exists('excel')) {
            return $report->excel();
        }

        $data = $report->run();

        return view('reports.cost-control.threshold.index', $data);
    }

    function wasteIndexReport(Project $project, Request $request)
    {
        $period = Period::find($this->getPeriod($project, $request));
        $report = new WasteIndexReport($period);

        if ($request->exists('excel')) {
            return $report->excel();
        }

        $data = $report->run();

        return view('reports.cost-control.waste-index.index', $data);
    }

    function productivityIndexReport(Project $project, Request $request)
    {
        $period = Period::find($this->getPeriod($project, $request));
        $report = new ProductivityIndexReport($period);

        if ($request->exists('excel')) {
            return $report->excel();
        }

        $data = $report->run();

        return view('reports.cost-control.productivity-index.index', $data);
    }


    public function chart(Project $project, Request $request)
    {
        $query = MasterShadow::query();

        $typeMethod = camel_case($request->type . '_chart');
        $filter = $request->filter;
        $filterMethod = camel_case($filter. '_chart_filter');

        $query->where('master_shadows.project_id', $project->id)->$typeMethod($request->get('period_id', 0))
            ->$filterMethod($request->get('filter_items', []));

        /** @var Collection $data */
        $data = $query->get();
        $columns = [];
        if (!$data->isEmpty()) {
            $keys = array_keys($data->first()->getAttributes());
            $columns = collect($keys)->filter(function ($column) use ($filter) {
                return $column != $filter;
            });
        }

        return ['ok' => true, 'data' => $data, 'columns' => $columns, 'filter' => $filter];
    }


}
