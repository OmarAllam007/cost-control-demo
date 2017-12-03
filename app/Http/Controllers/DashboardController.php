<?php

namespace App\Http\Controllers;

use App\BreakDownResourceShadow;
use App\BudgetRevision;
use App\Http\Requests;
use App\MasterShadow;
use App\Period;
use App\Project;
use App\ResourceType;
use App\Revision\RevisionBreakdownResourceShadow;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Fluent;

class DashboardController extends Controller
{
    /** @var Collection */
    private $projects;

    /** @var Collection */
    private $last_period_ids;

    function index()
    {
        $this->projects = Project::all();
        $this->last_period_ids = Period::last()->pluck('period_id');

        if (request()->exists('clear')) {
            \Cache::forget('dashboard-data');
        }

        $data = \Cache::remember('dashboard-data', Carbon::parse('+1 day'), function () {
            return $this->getData();
        });

        return view('dashboard.index', $data);
    }

    private function getData()
    {
        return [
            'projectNames' => $this->projects->pluck('name', 'id'),
            'contracts_info' => $this->contracts_info(),
            'finish_dates' => $this->finish_dates(),
            'budget_info' => $this->budget_info(),
            'cost_info' => $this->cost_info(),
            'cost_summary' => $this->cost_summary()
        ];
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
        return $this->projects->filter(function ($project) {
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
        $general_requirement = RevisionBreakdownResourceShadow::whereIn('revision_id', $min_revision_ids)->where('resource_type_id', 1)->sum('budget_cost');
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
        $general_requirement = RevisionBreakdownResourceShadow::whereIn('revision_id', $max_revision_ids)->where('resource_type_id', 1)->sum('budget_cost');
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

    private function cost_info()
    {
        
//        $last_periods = Period::whereIn('id', $this->last_period_ids);

        $cpis = MasterShadow::whereIn('period_id', $this->last_period_ids)
            ->selectRaw('project_id, sum(allowable_ev_cost) as allowable_cost, sum(to_date_cost) as to_date_cost')
            ->groupBy('project_id')
            ->get()->map(function ($period) {
                $period->cpi = $period->allowable_cost / $period->to_date_cost;
                $period->variance = $period->allowable_cost - $period->to_date_cost;
                return $period;
            })->sortBy('cpi');

        $allowable_cost = $cpis->sum('allowable_cost');
        $to_date_cost = $cpis->sum('to_date_cost');
        $variance = $allowable_cost - $to_date_cost;
        $cpi = $allowable_cost / $to_date_cost;

        $highest_risk = $cpis->first();
        $lowest_risk = $cpis->last();

        return compact('allowable_cost', 'to_date_cost', 'variance', 'cpi', 'highest_risk', 'lowest_risk');
    }

    private function cost_summary()
    {
        $resourceTypes = ResourceType::parents()->pluck('name', 'id');

        $budgetSummary = BreakDownResourceShadow::selectRaw('resource_type_id, sum(budget_cost) as budget_cost')
            ->groupBy('resource_type_id')->orderBy('resource_type_id')->get()->keyBy('resource_type_id');

        $previous_period_ids = Period::whereIn('id', $this->last_period_ids)->get()->map(function ($period) {
            return Period::readyForReporting()->where('project_id', $period->project_id)->where('id', '<', $period->id)->value('id');
        })->filter();

        $previousSummary = MasterShadow::whereIn('period_id', $previous_period_ids)
            ->selectRaw('resource_type_id, sum(to_date_cost) as previous_cost, sum(allowable_ev_cost) as previous_allowable')
            ->selectRaw('sum(allowable_var) as previous_var')
            ->groupBy('resource_type_id')->orderBy('resource_type_id')->get()->keyBy('resource_type_id');

        return MasterShadow::whereIn('period_id', $this->last_period_ids)
            ->selectRaw('resource_type_id, sum(budget_cost) as budget_cost, sum(to_date_cost) as to_date_cost')
            ->selectRaw('sum(allowable_ev_cost) as to_date_allowable, sum(allowable_var) as to_date_var')
            ->selectRaw('sum(remaining_cost) as remaining_cost, sum(completion_cost) as completion_cost')
            ->selectRaw('sum(cost_var) as completion_var')
            ->groupBy('resource_type_id')->orderBy('resource_type_id')->get()
            ->map(function($type) use ($previousSummary, $budgetSummary, $resourceTypes) {
                $previous = $previousSummary->get($type->resource_type_id, new Fluent());
                $type->budget_cost = $budgetSummary->get($type->resource_type_id, new Fluent)->budget_cost ?: 0;
                $type->previous_cost = $previous->previous_cost ?: 0;
                $type->previous_allowable = $previous->previous_allowable ?: 0;
                $type->previous_var = $previous->previous_var ?: 0;
                $type->resource_type = $resourceTypes->get($type->resource_type_id);

                return $type;
            });
    }

}
