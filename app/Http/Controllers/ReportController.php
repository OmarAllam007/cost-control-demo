<?php

namespace App\Http\Controllers;

use App\ActivityDivision;
use App\Boq;
use App\Breakdown;
use App\BreakdownResource;
use App\BreakDownResourceShadow;
use App\Http\Controllers\Reports\ActivityResourceBreakDown;
use App\Http\Controllers\Reports\BoqPriceList;
use App\Http\Controllers\Reports\BudgetCostByBreakDownItem;
use App\Http\Controllers\Reports\BudgetCostByBuilding;
use App\Http\Controllers\Reports\BudgetCostByDiscipline;
use App\Http\Controllers\Reports\BudgetCostDryCostByBuilding;
use App\Http\Controllers\Reports\BudgetCostDryCostByDiscipline;
use App\Http\Controllers\Reports\BudgetCostDryCostDiscipline;
use App\Http\Controllers\Reports\BudgetSummeryReport;
use App\Http\Controllers\Reports\HighPriorityMaterials;
use App\Http\Controllers\Reports\Productivity;
use App\Http\Controllers\Reports\QtyAndCost;
use App\Http\Controllers\Reports\QuantitiySurveySummery;
use App\Http\Controllers\Reports\ResourceDictionary;
use App\Http\Controllers\Reports\RevisedBoq;
use App\Project;
use App\Resources;
use App\ResourceType;
use App\StdActivity;
use App\StdActivityResource;
use App\Unit;
use App\WbsLevel;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Input;

class ReportController extends Controller
{
    private $request;
    private $project;

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
        set_time_limit(300);
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
        set_time_limit(300);
        $resources = [];
        $root = '';
        $total_budget_cost = '';
        $total_budget_unit = '';
        $breakdown_resources = \DB::select('SELECT
  resource_id,
  resource_name,
  resource_type,
  budget_cost,
  budget_unit,
  measure_unit
FROM break_down_resource_shadows
WHERE project_id=' . $project->id . '
AND resource_type LIKE \'%lab%\'');
        foreach ($breakdown_resources as $resource) {
            $rootName = $resource->resource_type;
            $root = $rootName;
            if (!isset($resources[$resource->resource_id])) {
                $resources[$resource->resource_id] = [
                    'id' => $resource->resource_id,
                    'name' => $resource->resource_name,
                    'type' => $rootName,
                    'budget_cost' => 0,
                    'budget_unit' => 0,
                    'unit' => $resource->measure_unit??'',
                ];


                $resources[$resource->resource_id]['budget_cost'] += $resource->budget_cost;
                $resources[$resource->resource_id]['budget_unit'] += $resource->budget_unit;
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
        $budgetSummery = new BudgetSummeryReport();
        return $budgetSummery->getReport($project);
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
        $budget_cost = new BudgetCostDryCostByDiscipline($project);
        return $budget_cost->run();
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

    public function highPriority(Project $project, Request $request)
    {
        $resources = Resources::where('project_id', $project->id)->whereNotNull('top_material')->pluck('id')->toArray();
        if (count($resources)) {
            return $this->topMaterialResources($project, $request, $resources);
        } else {
            $high_materials = new HighPriorityMaterials();
            return $high_materials->getTopHighPriorityMaterials($project, $request);
        }

    }

    public function topMaterialResources(Project $project, Request $request, $resources = false)
    {
        if($request->get('checked')){
            $resources = Resources::where('project_id', $project->id)->whereIn('id', $request['checked'])->get();
            Resources::flushEventListeners();
            foreach ($resources as $resource) {
                $resource->top_material = $resource->types->name;
                $resource->update();
            }
        }
        if (!$request->get('checked')) {
            $request['checked'] = $resources;
        }
        $tree = [];
        $this->project = $project;
        $this->request = $request;

        $resource_types = ResourceType::tree()->with('children', 'children.children', 'children.children.children')->get();
        $types = $resource_types->where('name', '03.MATERIAL');
        foreach ($types as $type) {
            $level = $this->getTree($type, $request);
            $tree[] = $level;
        }



        return view('reports.budget.high_priority_materials.top_resources', compact('tree', 'project'));


    }

    public function topMaterialResourcesReset(Project $project)
    {
        Resources::flushEventListeners();
        Resources::where('project_id', $project->id)->whereNotNull('top_material')->update(['top_material' => null]);
        return redirect()->route('project.show', $project);
    }

    private function getTree($type, $request)
    {
        $tree = ['id' => $type->id, 'name' => $type->name, 'children' => [], 'resources' => [], 'budget_cost' => 0, 'budget_unit' => 0];
        foreach ($request->get('checked') as $resource) {
            $shadow = \DB::select('SELECT
  r.name AS resource_name,
  r.id AS resource_id,
  r.resource_type_id,
  t.name type_name,
  sum(sh.budget_cost) AS budget_cost , 
  sum(sh.budget_unit) AS budget_unit
FROM resources r, break_down_resource_shadows sh, resource_types t
WHERE sh.project_id = ?
      AND r.id = sh.resource_id
      AND t.id = r.resource_type_id
      AND t.id = ?
      AND r.id = ?
GROUP BY r.name, r.resource_type_id  , t.name , r.id', [$this->project->id, $type->id, $resource]);
            if(isset($shadow[0])){
                if (!isset($tree['resources'][$shadow[0]->resource_name])) {
                    $tree['resources'][$shadow[0]->resource_name] = [
                        'id' => $shadow[0]->resource_id,
                        'name' => $shadow[0]->resource_name,
                        'budget_cost' => $shadow[0]->budget_cost,
                        'budget_unit' => $shadow[0]->budget_unit,
                    ];
                    $tree['budget_cost'] += $shadow[0]->budget_cost;
                    $tree['budget_unit'] += $shadow[0]->budget_unit;
                }
            }
        }


        if ($type->children->count()) {
            $tree['children'] = $type->children->map(function (ResourceType $childLevel) use ($request) {
                return $this->getTree($childLevel, $request);
            });

            foreach ($tree['children'] as $child) {
                $tree['budget_cost'] += $child['budget_cost'];
            }
        }
        return $tree;
    }

}
