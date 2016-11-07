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
        $total_budget_cost = '';
        $total_budget_unit = '';
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
        $data = [];
        $breakdowns = $project->breakdown_resources;
        foreach ($breakdowns as $breakdown) {
            $std_acivity_division = $breakdown->breakdown->std_activity->division;

            if (!isset($data[ $std_acivity_division->parent->name ])) {
                $data[ $std_acivity_division->parent->name ] = [
                    'id'=>$std_acivity_division->parent->id,
                    'name' => $std_acivity_division->parent->name,
                    'budget_cost' => 0,
                    'name' => $std_acivity_division->name,
                    'divisions' => [],

                ];
            }
//fill divisions
            foreach (ActivityDivision::where('parent_id', $std_acivity_division->parent->id)->get() as $item) {
                if (!isset($data[ $std_acivity_division->parent->name ]['divisions'][ $item->name ])) {
                    $data[ $std_acivity_division->parent->name ]['divisions'][ $item->name ] = [
                        'id'=>$item->id,
                        'name' => $item->name,
                        'budget_cost' => 0,
                        'activities' => [],
                    ];
                }
                foreach ($std_acivity_division->activities as $activity) {
                    if (!isset($data[ $std_acivity_division->parent->name ]['divisions'][ $item->name ]['activities'][ $activity->name ])) {
                        $data[ $std_acivity_division->parent->name ]['divisions'][ $item->name ]['activities'][ $activity->name ] = [
                            'activity_name' => $activity->name,
                            'budget_cost' => $activity->getBudgetCost($project->id),
                        ];
                    }
                }
            }

        }

//        dd($data);
        return view('std-activity.budgetSummery', compact('data', 'project'));
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
