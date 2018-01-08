<?php

namespace App\Reports\Cost;

use App\ActualRevenue;
use App\BreakDownResourceShadow;
use App\BudgetRevision;
use App\GlobalPeriod;
use App\MasterShadow;
use App\Period;
use App\Project;
use App\Revision\RevisionBreakdownResourceShadow;
use Carbon\Carbon;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Collection;
use Illuminate\Support\Fluent;

class GlobalReport
{
    /** @var Collection */
    protected $cpi_trend;

    /** @var Collection */
    protected $waste_index_trend = null;

    /** @var Collection */
    private $projects;

    /** @var Collection */
    private $last_period_ids;

    /** @var GlobalPeriod */
    private $period;

    /** @var Collection */
    private $periods;

    function __construct(GlobalPeriod $period)
    {
        $this->period = $period;
    }

    function run()
    {
        $this->projects = Project::all();

        $this->last_period_ids = Period::readyForReporting()->where('global_period_id', $this->period->id)->pluck('id');
        $this->periods = Period::find($this->last_period_ids->toArray());

        return [
            'projectNames' => $this->projects->pluck('name', 'id'),
            'contracts_info' => $this->contracts_info(),
            'finish_dates' => $this->finish_dates(),
            'budget_info' => $this->budget_info(),
            'cost_summary' => $this->cost_summary(),
            'cost_info' => $this->cost_info(),
            'cost_percentage_chart' => $this->cost_percentage(),
            'cpi_trend' => $this->cpi_trend(),
            'spi_trend' => $this->spi_trend(),
            'waste_index_trend' => $this->waste_index_trend(),
            'pi_trend' => $this->productivity_index_trend(),
            'actual_revenue_trend' => $this->actual_revenue_trend()
        ];
    }

    private function contracts_info()
    {
        $contracts_total = $this->projects->sum('project_contract_signed_value');
        $change_orders = $this->periods->sum('change_order_amount');

        $revised = $contracts_total + $change_orders;
        $budget_total = BreakDownResourceShadow::sum('budget_cost');

        $profit = $budget_total - $contracts_total - $change_orders;
        $profitability = $profit * 100 / $contracts_total;
        $finish_date = Carbon::parse($this->projects->max('expected_finished_date'));

        $schedules = $this->periods->map(function ($period) {
            $schedule = new Fluent();
            $schedule->project_name = $period->project->name;
            $schedule->planned_start = Carbon::parse($period->project->project_start_date)->format('d M Y');
            $schedule->original_duration = $period->project->project_duration;
            $schedule->planned_finish = Carbon::parse($period->planned_finish_date)->format('d M Y');
            $schedule->actual_start = Carbon::parse($period->project->actual_start_date)->format('d M Y');
            $schedule->expected_duration = $period->expected_duration;
            $schedule->forecast_finish = $period->forecast_finish_date? Carbon::parse($period->forecast_finish_date)->format('d M Y') : '';
            $schedule->delay_variance = $period->duration_variance;

            return $schedule;
        })->sortBy('project_name');

        return compact('contracts_total', 'change_orders', 'revised', 'profit', 'profitability', 'finish_date', 'schedules');
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
        $cpis = $this->cost_summary->map(function ($period) {
            $period->cpi = $period->allowable_cost / $period->to_date_cost;
            $period->variance = $period->allowable_cost - $period->to_date_cost;
            return $period;
        })->sortBy('cpi');

        $allowable_cost = $cpis->sum('allowable_cost');
        $to_date_cost = $cpis->sum('to_date_cost');
        $variance = $allowable_cost - $to_date_cost;
        $cpi = $allowable_cost / $to_date_cost;
        $pw_index = $this->waste_index_trend()->last();

        $highest_risk = $cpis->first();
        $lowest_risk = $cpis->last();

        $total_budget = $this->cost_summary->sum('budget_cost');
        $to_date = $this->cost_summary->sum('to_date_cost');

        $actual_progress = round($to_date * 100 / $total_budget, 2);
        $planned_progress = round($this->period->planned_progress ?: 0, 2);

        $progress = [$actual_progress, $planned_progress];

        return compact('allowable_cost', 'to_date_cost', 'variance', 'cpi', 'highest_risk', 'lowest_risk', 'pw_index', 'progress');
    }

    private function cost_summary()
    {
        return $this->cost_summary = MasterShadow::from('master_shadows as sh')->join('projects as p', 'sh.project_id', '=', 'p.id')
            ->whereIn('period_id', $this->last_period_ids)
            ->selectRaw('sh.project_id, p.name as project_name, sum(budget_cost) as budget_cost, sum(to_date_cost) as to_date_cost')
            ->selectRaw('sum(allowable_ev_cost) as allowable_cost, sum(allowable_var) as to_date_var')
            ->selectRaw('sum(remaining_cost) as remaining_cost, sum(completion_cost) as completion_cost')
            ->selectRaw('sum(cost_var) as completion_var')
            ->groupBy('sh.project_id', 'p.name')->orderBy('p.name')->get();

    }

    private function cost_percentage()
    {
        $to_date_cost = $this->cost_summary->sum('to_date_cost');
        $remaining_cost = $this->cost_summary->sum('remaining_cost');
        $sum = $to_date_cost + $remaining_cost;

        return collect([round($to_date_cost * 100 / $sum, 2), round($remaining_cost * 100 / $sum, 2)]);
    }

    private function cpi_trend()
    {
        $periods = GlobalPeriod::latest()->take(12)->get()->keyBy('id');
        $global_period_ids = $periods->pluck('id');
        $period_ids = Period::whereIn('global_period_id', $global_period_ids)->readyForReporting()->pluck('id');

        return $this->cpi_trend = MasterShadow::from('master_shadows as sh')
            ->selectRaw('p.global_period_id, sum(to_date_cost) as to_date_cost, sum(allowable_ev_cost) as allowable_cost')
            ->join('periods as p', 'sh.period_id', '=', 'p.id')
            ->whereIn('sh.period_id', $period_ids)
            ->groupBy('p.global_period_id')
            ->get()->map(function ($period) use ($periods) {
                $period->cpi_index = round($period->allowable_cost / $period->to_date_cost, 2);
                $period->name = $periods->get($period->global_period_id)->name;
                return $period;
            });
    }

    function spi_trend()
    {
        return GlobalPeriod::latest()->take(12)->pluck('spi_index', 'name');
    }

    function waste_index_trend()
    {
        if ($this->waste_index_trend !== null) {
            return $this->waste_index_trend;
        }

        $periods = GlobalPeriod::latest()->take(12)->get()->keyBy('id');
        $global_period_ids = $periods->pluck('id');
        $period_ids = Period::whereIn('global_period_id', $global_period_ids)->readyForReporting()->pluck('id');

        return $this->waste_index_trend = MasterShadow::from('master_shadows as sh')
            ->select('p.global_period_id')
            ->selectRaw('sum((sh.allowable_qty - sh.to_date_qty) * sh.to_date_unit_price) as variance, sum(sh.allowable_ev_cost) as allowable_cost')
            ->join('periods as p', 'sh.period_id', '=', 'p.id')
            ->whereIn('sh.period_id', $period_ids)
            ->where('resource_type_id', 3)
            ->groupBy('p.global_period_id')
            ->orderBy('p.global_period_id')
            ->get()->map(function ($period) use ($periods) {
                $period->waste_index = 0;
                if ($period->allowable_cost) {
                    $period->waste_index = round($period->variance * 100 / $period->allowable_cost, 4);
                }

                $period->name = $periods->get($period->global_period_id)->name;
                return $period;
            })->pluck('waste_index', 'name');
    }

    function productivity_index_trend()
    {
        $periods = GlobalPeriod::latest()->take(12)->get()->keyBy('id');
        $global_period_ids = $periods->pluck('id');
        $period_ids = Period::whereIn('global_period_id', $global_period_ids)->readyForReporting()->pluck('id');

        return MasterShadow::from('master_shadows as sh')
            ->join('periods as p', 'sh.period_id', '=', 'p.id')
            ->join('cost_man_days as md', function(JoinClause $on) {
                $on->where('sh.period_id', '=', 'md.period-id')
                    ->where('sh.wbs_id', '=', 'md.wbs_id')
                    ->where('sh.activity_id', '=', 'md.activity_id');
            })
            ->selectRaw("p.global_period_id, sum(budget_unit) as budget_unit, sum(allowable_qty) as allowable_qty, sum(actual) as actual")
            ->where('sh.resource_type_id', 2)->where('to_date_cost', '>', 0)
            ->whereIn('sh.period_id', $period_ids)
            ->groupBy('p.global_period_id')
            ->get()->map(function ($period) use ($periods) {
                $period->name = $periods->get($period->global_period_id)->name;
                $period->pi = 0;
                if ($period->actual) {
                    $period->pi = $period->allowable_qty / $period->actual;
                }

                return $period;
            })->pluck('pi', 'name');
    }

    function actual_revenue_trend()
    {
        $periods = GlobalPeriod::latest()->take(12)->get()->keyBy('id');
        $global_period_ids = $periods->pluck('id');
        $period_ids = Period::whereIn('global_period_id', $global_period_ids)->readyForReporting()->pluck('id');

        return ActualRevenue::from('actual_revenue as r')
            ->join('periods as p', 'r.period_id', '=', 'p.id')
            ->whereIn('period_id', $period_ids)
            ->selectRaw('p.global_period_id, sum(value) as value')
            ->groupBy('p.global_period_id')->get(['value', 'global_period_id'])
            ->map(function($period) use ($periods) {
                $period->name = $periods->get($period->global_period_id)->name;
                return $period;
            })->pluck('value', 'name');
    }
}