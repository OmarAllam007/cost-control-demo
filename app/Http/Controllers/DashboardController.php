<?php

namespace App\Http\Controllers;

use App\BreakDownResourceShadow;
use App\BudgetRevision;
use App\Project;
use App\Revision\RevisionBreakdownResourceShadow;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Collection;

class DashboardController extends Controller
{
    /** @var Collection */
    private $projects;

    function index()
    {
        $this->projects = Project::all();

        $contracts_info = $this->contracts_info();
        $finish_dates = $this->finish_dates();
        $budget_info = $this->budget_info();

        return view('dashboard.index', compact('contracts_info', 'finish_dates', 'budget_info'));
    }

    private function contracts_info()
    {
        $contracts_total = $this->projects->sum('project_contract_signed_value');
        $change_orders = $this->projects->sum('change_order_amount');
        $revised = $contracts_total + $change_orders;
        $budget_total = BreakDownResourceShadow::sum('budget_cost');

        $profit = $budget_total - $contracts_total - $change_orders;
        $profitability = $profit * 100 / $contracts_total;
        $finish_date = Carbon::parse($this->projects->max('expected_finished_date'));

        return compact('contracts_total', 'change_orders', 'revised', 'profit', 'profitability', 'finish_date');
    }


    private function finish_dates()
    {
        return $this->projects->filter(function($project) {
            return $project->project_start_date != '0000-00-00' && $project->expected_finished_date != '0000-00-00';
        })->map(function ($project) {
            return [
                'title' => $project->project_code,
                'start_date' => Carbon::parse($project->project_start_date)->format('d M Y'),
                'finish_date' => Carbon::parse($project->expected_finished_date)->format('d M Y'),
            ];
        });
    }

    private function budget_info()
    {
        $min_revision_ids = BudgetRevision::minRevisions()->pluck('id');
        $min_revisions = BudgetRevision::find($min_revision_ids->toArray());
        $general_requirement =  RevisionBreakdownResourceShadow::whereIn('revision_id', $min_revision_ids)->where('resource_type_id', 1)->sum('budget_cost');
        $management_reserve = RevisionBreakdownResourceShadow::whereIn('revision_id', $min_revision_ids)->where('resource_type_id', 8)->sum('budget_cost');
        $budget_cost = RevisionBreakdownResourceShadow::whereIn('revision_id', $min_revision_ids)->sum('budget_cost');
        $revised_contracts = $min_revisions->sum('revised_contract_amount');
        $profit = $revised_contracts - $budget_cost;
        $revision0 = [
            'budget_cost' => $budget_cost,
            'direct_cost' => $budget_cost - $general_requirement - $management_reserve,
            'indirect_cost' => $general_requirement + $management_reserve,
            'profit' => $profit,
            'profitability' => $profit * 100 / $revised_contracts,
        ];

        $max_revision_ids = BudgetRevision::maxRevisions()->pluck('id');
        $max_revisions = BudgetRevision::find($max_revision_ids->toArray());
        $general_requirement =  RevisionBreakdownResourceShadow::whereIn('revision_id', $max_revision_ids)->where('resource_type_id', 1)->sum('budget_cost');
        $management_reserve = RevisionBreakdownResourceShadow::whereIn('revision_id', $max_revision_ids)->where('resource_type_id', 8)->sum('budget_cost');
        $budget_cost = RevisionBreakdownResourceShadow::whereIn('revision_id', $max_revision_ids)->sum('budget_cost');
        $revised_contracts = $max_revisions->sum('revised_contract_amount');
        $profit = $revised_contracts - $budget_cost;
        $revision1 = [
            'budget_cost' => $budget_cost,
            'direct_cost' => $budget_cost - $general_requirement - $management_reserve,
            'indirect_cost' => $general_requirement + $management_reserve,
            'profit' => $profit,
            'profitability' => $profit * 100 / $revised_contracts,
        ];

        return compact('revision0', 'revision1');
    }

    function dashboard()
    {
        $projectNames = Project::orderBy('name')->pluck('name', 'id');
        $projectStats = collect(\DB::select('SELECT sh.project_id, sum(budget_cost) AS budget_cost, sum(cost) AS actual_cost FROM break_down_resource_shadows sh LEFT JOIN actual_resources ar ON (sh.breakdown_resource_id = ar.breakdown_resource_id) GROUP BY sh.project_id'))
            ->keyBy('project_id')->map(function ($project) {
                return ['budget_cost' => $project->budget_cost ?: 0, 'actual_cost' => $project->actual_cost ?: 0];
            });

        $topActivities = \DB::select('SELECT activity, sum(budget_cost) AS budget_cost, sum(cost) AS actual_cost FROM break_down_resource_shadows sh LEFT JOIN actual_resources ar ON (sh.breakdown_resource_id = ar.breakdown_resource_id) GROUP BY activity ORDER BY 2 DESC, 1 LIMIT 10');
        $topResources = \DB::select('SELECT resource_name, sum(budget_cost) AS budget_cost, sum(cost) AS actual_cost FROM break_down_resource_shadows sh LEFT JOIN actual_resources ar ON (sh.breakdown_resource_id = ar.breakdown_resource_id) GROUP BY resource_name ORDER BY 2 DESC, 1 LIMIT 10');
        $resourceTypes = \DB::select('SELECT resource_type, sum(budget_cost) AS budget_cost, sum(cost) AS actual_cost FROM break_down_resource_shadows sh LEFT JOIN actual_resources ar ON (sh.breakdown_resource_id = ar.breakdown_resource_id) GROUP BY resource_type ORDER BY 1');

        return view('dashboard', compact('projectNames', 'projectStats', 'topActivities', 'topResources', 'resourceTypes'));
    }



}
