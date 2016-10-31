<?php

namespace App\Http\Controllers;

use App\ActivityDivision;
use App\Boq;
use App\Breakdown;
use App\BreakdownResource;
use App\Http\Controllers\Reports\ActivityResourceBreakDown;
use App\Http\Controllers\Reports\BoqPriceList;
use App\Http\Controllers\Reports\BudgetCostByBreakDownItem;
use App\Http\Controllers\Reports\BudgetCostByBuilding;
use App\Http\Controllers\Reports\BudgetCostByDiscipline;
use App\Http\Controllers\Reports\BudgetCostDryCostByBuilding;
use App\Http\Controllers\Reports\BudgetCostDryCostByDiscipline;
use App\Http\Controllers\Reports\BudgetCostDryCostDiscipline;
use App\Http\Controllers\Reports\HighPriorityMaterials;
use App\Http\Controllers\Reports\Productivity;
use App\Http\Controllers\Reports\QtyAndCost;
use App\Http\Controllers\Reports\QuantitiySurveySummery;
use App\Http\Controllers\Reports\ResourceDictionary;
use App\Http\Controllers\Reports\RevisedBoq;
use App\Project;
use App\Resources;
use App\StdActivity;
use App\StdActivityResource;
use App\WbsLevel;
use Illuminate\Http\Request;

use App\Http\Requests;

class ReportController extends Controller
{
    public function wbsReport(Project $project)
    {
        return view('wbs-level.report', compact('project'));
    }

    public function productivityReport(Project $project)
    {
        $productivity = new Productivity();
        return $productivity->getProductivity($project);
    }

    public function stdActivityReport(Project $project)
    {
        $div_ids = $project->getDivisions();
        $activity_ids = $project->getActivities()->toArray();
        $all = $div_ids['all'];
        $parent_ids = $div_ids['parents'];

        $parents = ActivityDivision::whereIn('id', $parent_ids)->get();

        return view('std-activity.report', compact('parents', 'all', 'activity_ids', 'project'));
    }

    public function resourceDictionary(Project $project)
    {
        $resource = new ResourceDictionary();
        return $resource->getResourceDictionary($project);
    }


    public function manPower(Project $project)
    {
        $resources = [];
        $root = '';

        foreach ($project->breakdown_resources as $resource) {
            $rootName = $resource->resource->types->root->name;
            if (str_contains($rootName, 'LABORS')) {
                $root = $rootName;
                $resourceObject = $resource->resource;
                if (!isset($resources[ $resourceObject->id ])) {
                    $resources[ $resourceObject->id ] = [
                        'id' => $resourceObject->id,
                        'name' => $resourceObject->name,
                        'type' => $rootName,
                        'budget_cost' => 0,
                        'budget_unit' => 0,
                        'unit' => $resource->project_resource->units->type,
                    ];
                }
                $total_budget_cost = '';
                $total_budget_unit = '';
                $resources[ $resourceObject->id ]['budget_cost'] += $resource->budget_cost;
                $resources[ $resourceObject->id ]['budget_unit'] += $resource->budget_unit;

                foreach ($resources as $resource) {
                    $total_budget_cost += $resource['budget_cost'];
                    $total_budget_unit += $resource['budget_unit'];
                }

            }
        }


        return view('resources.manpower_report', compact('project', 'resources', 'root', 'total_budget_cost', 'total_budget_unit'));
    }

    public function budgetSummery(Project $project)
    {
        $div_ids = $project->getDivisions();
        $activity_ids = $project->getActivities()->toArray();
        $all = $div_ids['all'];
        $parent_ids = $div_ids['parents'];

        $allDivisions = ActivityDivision::whereIn('id', $all)->get();
        $parents = ActivityDivision::whereIn('id', $parent_ids)->get();

        $std_array = $project->breakdowns()->with('std_activity')->pluck('std_activity_id')->toArray();
        $std_activities = StdActivity::whereIn('id', $std_array)->get();
        $std_activity_cost = [];
        $stds_activity_cost = [];
        $activities = [];
        $parents_cost = [];

        foreach ($allDivisions as $allDivision) {
            $childrenIds = ActivityDivision::where('parent_id', $allDivision->id)->get()->pluck('id');
            if (!isset($std_activity_cost[ $allDivision->id ])) {
                $std_activity_cost[ $allDivision->id ] = [
                    'budget_cost' => 0,
                ];
                $stds_activity_cost[ $allDivision->id ] = [
                    'name' => $allDivision->name,
                    'total_cost' => 0,
                    'divisions' => [
                        'name' => $allDivision->children,
                        'budget_cost' => 0,
                        'budget_cost' => 0,
                    ],
                ];


            }
            foreach ($std_activities->where('division_id', $allDivision->id) as $activity) {
                if (!isset($activities[ $activity->id ])) {
                    $activities[ $activity->id ] = [
                        'budget_cost' => 0,
                    ];
                }
                $activities[ $activity->id ]['budget_cost'] += $activity->getBudgetCost($project->id);
                $std_activity_cost[ $allDivision->id ]['budget_cost'] += $activities[ $activity->id ]['budget_cost'];

            }

        }


        return view('std-activity.budgetSummery', compact('parents', 'all', 'activity_ids', 'activities', 'std_activity_cost', 'project'));
    }

    public function activityResourceBreakDown(Project $project)
    {
        $activity = new ActivityResourceBreakDown();
        return $activity->getActivityResourceBreakDown($project);
    }

    public function boqPriceList(Project $project)
    {
        $boq_price_list = new BoqPriceList();
        return $boq_price_list->getBoqPriceList($project);
    }


    public function qsSummery(Project $project)
    {
        $quantity_survey = new QuantitiySurveySummery();
        return $quantity_survey->qsSummeryReport($project);
    }

    public function budgetCostVSDryCost(Project $project)
    {
        $budget_cost = new BudgetCostDryCostByBuilding();
        return $budget_cost->compareBudgetCostDryCost($project);
    }

    public function budgetCostVSBreadDown(Project $project)
    {
        $budget_breakDown = new  BudgetCostByBreakDownItem();
        return $budget_breakDown->compareBudgetCostByBreakDownItem($project);
    }

    public function budgetCostDiscipline(Project $project)
    {
        $budget_breakDown = new  BudgetCostByDiscipline();
        return $budget_breakDown->compareBudgetCostDiscipline($project);
    }

    public function budgetCostDryCostDiscipline(Project $project)
    {
        $budget_cost = new BudgetCostDryCostByDiscipline();
        return $budget_cost->compareBudgetCostDryCostDiscipline($project);
    }

    public function budgetCostForBuilding(Project $project)
    {
        $budget = new BudgetCostByBuilding();
        return $budget->getBudgetCostForBuilding($project);
    }

    public function quantityAndCostByDiscipline(Project $project)
    {
        $discipline = new QtyAndCost();
        return $discipline->compare($project);
    }

    public function revisedBoq(Project $project)
    {
        $boq = new  RevisedBoq();
        return $boq->getRevised($project);
    }

    public function highPriority(Project $project)
    {
        $high_materials = new HighPriorityMaterials();
        return $high_materials->getTopHighPriorityMaterials($project);
    }


}
