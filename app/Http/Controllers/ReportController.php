<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Reports\BudgetCostByBreakDownItem;
use App\Http\Controllers\Reports\BudgetCostByBuilding;
use App\Http\Controllers\Reports\BudgetCostByDiscipline;
use App\Http\Controllers\Reports\BudgetCostDryCostByBuilding;
use App\Http\Controllers\Reports\BudgetCostDryCostByDiscipline;
use App\Http\Controllers\Reports\BudgetCostDryCostDiscipline;
use App\Http\Controllers\Reports\BudgetSummeryReport;
use App\Http\Controllers\Reports\HighPriorityMaterials;
use App\Http\Controllers\Reports\QtyAndCost;
use App\Http\Controllers\Reports\RevisedBoq;
use App\Project;
use App\Reports\Budget\ActivityResourceBreakDownReport;
use App\Reports\Budget\BoqPriceListReport;
use App\Reports\Budget\BudgetTrendReport;
use App\Reports\Budget\ManPowerReport;
use App\Reports\Budget\ProductivityReport;
use App\Reports\Budget\QsSummaryReport;
use App\Reports\Budget\ResourceDictReport;
use App\Reports\Budget\StdActivityReport;
use App\Reports\Budget\WbsReport;
use App\Resources;
use App\ResourceType;
use Illuminate\Http\Request;

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
        $report = new WbsReport($project);

        if (request()->exists('excel')) {
            return $report->excel();
        }

        $data = $report->run();
        return view('reports.budget.wbs.index', $data);
    }

    public function productivityReport(Project $project)
    {
        $report = new ProductivityReport($project);
        $data = $report->run();

        if (request()->exists('excel')) {
            return $report->excel();
        }

        return view('reports.budget.productivity.index', $data);
    }

    public function stdActivityReport(Project $project)
    {
        $report = new StdActivityReport($project);
        $data = $report->run();

        if (request()->exists('excel')) {
            return $report->excel();
        }

        return view('reports.budget.std-activity.index', $data);
    }

    public function resourceDictionary(Project $project)
    {
        $report = new ResourceDictReport($project);
        $data = $report->run();

        if (request()->exists('excel')) {
            return $report->excel();
        }

        return view('reports.budget.resource-dict.index', $data);
    }

    public function qsSummary(Project $project)
    {
        $report = new QsSummaryReport($project);
        $data = $report->run();

        if (request()->exists('excel')) {
            return $report->excel();
        }

        return view('reports.budget.qs-summary.index', $data);
    }

    public function boqPriceList(Project $project)
    {
        $report = new BoqPriceListReport($project);
        $data = $report->run();

        if (request()->exists('excel')) {
            return $report->excel();
        }

        return view('reports.budget.boq_price_list.index', $data);
    }


    public function manPower(Project $project)
    {
        $report = new ManPowerReport($project);

        if (request()->exists('excel')) {
            return $report->excel();
        }

        $data = $report->run();
        return view('reports.budget.man_power.index', $data);
    }


    public function activityResourceBreakDown(Project $project)
    {
        $report = new ActivityResourceBreakDownReport($project);

        if (request()->exists('excel')) {
            return $report->excel();
        }

        $data = $report->run();

        return view('reports.budget.activity_resource_breakdown.index', $data);
    }

    public function budgetSummery(Project $project)
    {
        $budgetSummery = new BudgetSummeryReport();
        return $budgetSummery->getReport($project);
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

    function budgetTrend(Project $project)
    {
        $report = new BudgetTrendReport($project);
        $data = $report->run();

        return view('reports.budget.budget-trend.index', $data);
    }

}
