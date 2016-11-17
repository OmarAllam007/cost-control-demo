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

    public function getReports(Project $project)
    {
        return view('project.tabs._report', compact('project'));
    }

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
        $breakdown_resources = $project->breakdown_resources;
        foreach ($breakdown_resources as $resource) {
            $rootName = $resource->resource->types->root->name;
            $resourceObject = $resource->resource;
            if (str_contains($rootName, 'LABORS')) {
                $root = $rootName;
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
            }

        }

        foreach ($resources as $resource) {
            $total_budget_cost += $resource['budget_cost'];
            $total_budget_unit += $resource['budget_unit'];
        }

        return view('resources.manpower_report', compact('project', 'resources', 'root', 'total_budget_cost', 'total_budget_unit'));
    }

    public function budgetSummery(Project $project)
    {
        ini_set('max_execution_time', 300);
        ini_set('memory_limit', '-1');
        $data = [];
        $breakdowns = $project->breakdowns;
        $parent_name = '';
        foreach ($breakdowns as $breakdown) {
            $parent = $breakdown->std_activity->division;
            $division = $breakdown->std_activity->division;
            $activity = $breakdown->std_activity;
            while ($parent->parent) {
                $parent = $parent->parent;
                $parent_name = $parent->name;
                if (!isset($data[ $parent_name ])) {
                    $data[ $parent_name ] = [
                        'id' => $parent->id,
                        'name' => $parent_name,
                        'budget_cost' => 0,
                        'divisions' => [],
                    ];
                }
            }

            if (!isset($data[ $parent_name ]['divisions'][ $division->name ])) {
                $data[ $parent_name ]['divisions'][ $division->name ] = [
                    'division_name' => $division->name,
                    'budget_cost' => 0,
                    'activities' => [],
                ];
            }
            foreach ($breakdown->resources as $resource) {
                if (!isset($data[ $parent_name ]['divisions'][ $division->name ]['activities'][ $activity->name ])) {
                    $data[ $parent_name ]['divisions'][ $division->name ]['activities'][ $activity->name ] = [
                        'name' => $activity->name,
                        'budget_cost' => is_nan($resource->budget_cost) ? 0 : $resource->budget_cost,
                    ];
                }
                else { $data[ $parent_name ]['divisions'][ $division->name ]['activities'][ $activity->name ]['budget_cost'] += is_nan($resource->budget_cost) ? 0 : $resource->budget_cost;
                }
            }


        }

        foreach ($data as $key => $value) {//sum budget cost for arrays
            foreach ($data[ $key ]['divisions'] as $divKey => $divValue) {
                foreach ($data[ $key ]['divisions'][ $divKey ]['activities'] as $actKey => $actValue) {
                    $data[ $key ]['divisions'][ $divKey ]['budget_cost'] += $data[ $key ]['divisions'][ $divKey ]['activities'][ $actKey ]['budget_cost'];
                    $data[ $key ]['budget_cost'] += $data[ $key ]['divisions'][ $divKey ]['budget_cost'];
                }
            }
        }
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
