<?php

namespace App\Http\Controllers;


use App\Breakdown;
use App\Http\Controllers\Reports\ActivityResourceBreakDown;
use App\Http\Controllers\Reports\BoqPriceList;
use App\Http\Controllers\Reports\BudgetCostByBuilding;
use App\Http\Controllers\Reports\BudgetCostByDiscipline;
use App\Http\Controllers\Reports\BudgetCostDryCostByBuilding;
use App\Http\Controllers\Reports\BudgetCostDryCostByDiscipline;
use App\Http\Controllers\Reports\HighPriorityMaterials;
use App\Http\Controllers\Reports\Productivity;
use App\Http\Controllers\Reports\QtyAndCost;
use App\Http\Controllers\Reports\QuantitiySurveySummery;
use App\Http\Controllers\Reports\ResourceDictionary;
use App\Http\Controllers\Reports\RevisedBoq;
use App\Http\Requests\BreakdownRequest;
use App\Project;
use Illuminate\Http\Request;

class BreakdownController extends Controller
{

    public function create(Request $request)
    {
        if (!$request->has('project')) {
            return \Redirect::route('project.index');
        }

        $project = Project::find($request->get('project'));
        if (!$project) {
            flash('Project not found');
            return \Redirect::route('project.index');
        }
        return view('breakdown.create');
    }

    public function store(BreakdownRequest $request)
    {
        $breakdown = Breakdown::create($request->all());
        $breakdown->resources()->createMany($request->get('resources'));
        $breakdown->syncVariables($request->get('variables'));

        return \Redirect::to(route('project.show', $breakdown->project_id) . '#breakdown');
    }

    public function duplicate(Breakdown $breakdown)
    {
        return view('breakdown.duplicate', compact('breakdown'));
    }

    public function postDuplicate(Request $request, Breakdown $breakdown)
    {
        $this->validate($request, ['wbs_level_id' => 'required', 'cost_account' => 'required']);

        $duplicate = $breakdown->duplicate($request->only('wbs_level_id', 'cost_account'));

        flash('Breakdown has been duplicated', 'success');

        return \Redirect::to(route('breakdown.duplicate', $breakdown) . '?close=1');
    }

    public function edit(Breakdown $breakdown)
    {
        return view('breakdown.edit', compact('breakdown'));
    }

    public function update(Request $request, Breakdown $breakdown)
    {

    }

    public function delete(Breakdown $breakdown)
    {

    }

    function filters(Request $request, Project $project)
    {
        $data = $request->except('_token');
        \Session::set('filters.breakdown.' . $project->id, $data);

        return \Redirect::to(route('project.show', $project) . '#breakdown');
    }

    function printAll(Project $project)
    {
        //Budget Calculations
        $wbsLevelReport = new ReportController();
        $wbsLevelReportHtml = $wbsLevelReport->wbsReport($project)->render();

        $stdActivityReport = new ReportController();
        $stdActivityReportHtml = $stdActivityReport->stdActivityReport($project)->render();

        $productivity = new Productivity();
        $productivityHtml = $productivity->getProductivity($project)->render();

        $qsSummery = new QuantitiySurveySummery();
        $qsSummeryHtml = $qsSummery->qsSummeryReport($project)->render();

        $boqPriceList = new BoqPriceList();
        $boqPriceListHtml = $boqPriceList->getBoqPriceList($project)->render();
        //Budget Output
        $resourceDictionary = new ResourceDictionary();
        $resourceDictionaryHtml = $resourceDictionary->getResourceDictionary($project)->render();

        $highPriorityMaterials = new HighPriorityMaterials();
        $highPriorityMaterialsHtml = $highPriorityMaterials->getTopHighPriorityMaterials($project)->render();

        $budgetManPower = new ReportController();
        $budgetManPowerHtml = $budgetManPower->manPower($project)->render();

        $budgetSummery = new ReportController();
        $budgetSummeryHtml = $budgetSummery->budgetSummery($project)->render();

        $activityResourceBreakDown = new ActivityResourceBreakDown();
        $activityResourceBreakDownHtml = $activityResourceBreakDown->getActivityResourceBreakDown($project)->render();

        $revisedBoq = new RevisedBoq();
        $revisedBoqHtml = $revisedBoq->getRevised($project)->render();
        //Budget Reports

        $budgetCostByBuilding = new BudgetCostByBuilding();
        $budgetCostByBuildingHtml = $budgetCostByBuilding->getBudgetCostForBuilding($project)->render();

        $budgetCostByDiscipline = new BudgetCostByDiscipline();
        $budgetCostByDisciplineHtml = $budgetCostByDiscipline->compareBudgetCostDiscipline($project)->render();

        $budgetCostByBreakDown = new ReportController();
        $budgetCostByBreakDownHtml = $budgetCostByBreakDown->budgetCostVSBreadDown($project)->render();

        $budgetCostDryCostByBuilding = new BudgetCostDryCostByBuilding();
        $budgetCostDryCostByBuildingHtml = $budgetCostDryCostByBuilding->compareBudgetCostDryCost($project)->render();

        $budgetCostDryCostByDiscipline = new BudgetCostDryCostByDiscipline();
        $budgetCostDryCostByDisciplineHtml = $budgetCostDryCostByDiscipline->compareBudgetCostDryCostDiscipline($project)->render();

        $budgetCostVsDry = new QtyAndCost();
        $budgetCostVsDryHtml = $budgetCostVsDry->compare($project)->render();



        echo $wbsLevelReportHtml.
            $stdActivityReportHtml.
            $productivityHtml.
            $qsSummeryHtml.
            $boqPriceListHtml.
            $resourceDictionaryHtml.
            $highPriorityMaterialsHtml.
            $budgetManPowerHtml.
            $budgetSummeryHtml.
            $activityResourceBreakDownHtml.
            $revisedBoqHtml.
            $budgetCostByBuildingHtml.
            $budgetCostByDisciplineHtml.
            $budgetCostByBreakDownHtml.
            $budgetCostDryCostByBuildingHtml.
            $budgetCostDryCostByDisciplineHtml.
            $budgetCostVsDryHtml

        ;


    }
}