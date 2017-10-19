<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Reports\BudgetCostByBreakDownItem;
use App\Http\Controllers\Reports\BudgetCostByBuilding;
use App\Http\Controllers\Reports\BudgetSummeryReport;
use App\Http\Controllers\Reports\HighPriorityMaterials;
use App\Reports\Budget\BudgetCheckListReport;
use App\Reports\Budget\CharterReport;
use App\Reports\Budget\ComparisonReport;
use App\Reports\Budget\HighPriorityMaterialsReport;
use App\Reports\Budget\ProfitabilityIndexReport;
use App\Reports\Budget\QtyAndCostReport;
use App\Http\Controllers\Reports\RevisedBoq;
use App\Project;
use App\Reports\Budget\ActivityResourceBreakDownReport;
use App\Reports\Budget\BoqPriceListReport;
use App\Reports\Budget\BudgetCostByDisciplineReport;
use App\Reports\Budget\BudgetCostByResourceTypeReport;
use App\Reports\Budget\BudgetCostDryCostByBuildingReport;
use App\Reports\Budget\BudgetCostDryCostByDisciplineReport;
use App\Reports\Budget\BudgetTrendReport;
use App\Reports\Budget\ManPowerReport;
use App\Reports\Budget\ProductivityReport;
use App\Reports\Budget\QsSummaryReport;
use App\Reports\Budget\ResourceDictReport;
use App\Reports\Budget\RevisedBoqReport;
use App\Reports\Budget\StdActivityReport;
use App\Reports\Budget\WbsDictionary;
use App\Reports\Budget\WbsLabours;
use App\Reports\Budget\WbsReport;
use App\Resources;
use App\ResourceType;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function getReports(Project $project)
    {
        return view('project.tabs._report', compact('project'));
    }

    public function wbsReport(Project $project)
    {
        $report = new WbsReport($project, false);

        if (request()->exists('excel')) {
            return $report->excel();
        }

        $data = $report->run();
        return view('reports.budget.wbs.index', $data);
    }

    public function budgetCostByBuilding(Project $project)
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
        $report = new StdActivityReport($project, false);
        $data = $report->run();

        if (request()->exists('excel')) {
            return $report->excel();
        }

        return view('reports.budget.std-activity.index', $data);
    }

    public function budgetSummary(Project $project)
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

    function budgetCostDiscipline(Project $project)
    {
        $report = new  BudgetCostByDisciplineReport($project);

        if (request()->exists('excel')) {
            return $report->excel();
        }

        $data = $report->run();
        return view('reports.budget.budget_cost_by_discipline.index', $data);
    }

    function budgetCostByResourceType(Project $project)
    {
        $report = new BudgetCostByResourceTypeReport($project);

        if (request()->exists('excel')) {
            return $report->excel();
        }

        $data = $report->run();

        return view('reports.budget.budget_cost_by_resource_type.index', $data);
    }

    public function budgetCostVSDryCostByBuilding(Project $project)
    {
        $report = new BudgetCostDryCostByBuildingReport($project);

        if (request()->exists('excel')) {
            return $report->excel();
        }

        $data = $report->run();

        return view('reports.budget.budget_cost_vs_dr_by_building.index', $data);
    }

    public function budgetCostDryCostDiscipline(Project $project)
    {
        $report = new BudgetCostDryCostByDisciplineReport($project);

        if (request()->exists('excel')) {
            return $report->excel();
        }

        $data = $report->run();

        return view('reports.budget.budget_cost_dry_cost_by_discipline.index', $data);
    }

    public function quantityAndCostByDiscipline(Project $project)
    {
        $report = new QtyAndCostReport($project);

        if (request()->exists('excel')) {
            return $report->excel();
        }

        $data = $report->run();

        return view('reports.budget.quantity_and_cost_by_discipline.index', $data);
    }

    public function highPriorityMaterials(Project $project)
    {
        return $this->report(new HighPriorityMaterialsReport($project), 'reports.budget.high_priority_materials.index');
    }

    public function revisedBoq(Project $project)
    {
        return $this->report(new RevisedBoqReport($project), 'reports.budget.revised_boq.index');
    }

    public function wbsDictionary(Project $project)
    {
        return $this->report(new WbsDictionary($project), 'reports.budget.wbs-dictionary.index');
    }

    function wbsLabours(Project $project)
    {
        return $this->report(new WbsLabours($project), 'reports.budget.wbs-labours.index');
    }

    function profitability(Project $project)
    {
        return $this->report(new ProfitabilityIndexReport($project), 'reports.budget.profitability.index');
    }

    function charter(Project $project)
    {
        return $this->report(new CharterReport($project), 'reports.budget.charter.index');
    }

    function budgetTrend(Project $project)
    {
        $report = new BudgetTrendReport($project);

        if (request()->exists('excel')) {
            return $report->excel();
        }

        $data = $report->run();

        return view('reports.budget.budget-trend.index', $data);
    }
    
    function check_list(Project $project)
    {
        $report = new BudgetCheckListReport($project);
        return $report->run();
    }

    function comparison_report(Project $project)
    {
        return $this->report(new ComparisonReport($project), 'reports.budget.comparison.index');
    }

    protected function report($report, $view)
    {
        if (request()->exists('excel')) {
            return $report->excel();
        }

        return view($view, $report->run());
    }
}
