<?php
/**
 * Created by PhpStorm.
 * User: hazem
 * Date: 11/15/17
 * Time: 2:36 PM
 */

namespace App\Reports\Cost;


use App\ActualRevenue;
use App\BreakDownResourceShadow;
use App\BudgetRevision;
use App\CostManDay;
use App\MasterShadow;
use App\Period;
use App\Reports\Budget\ProfitabilityIndexReport;
use App\Revision\RevisionBreakdownResourceShadow;
use Carbon\Carbon;
use Illuminate\Support\Fluent;

class ProjectInfo
{
    /** @var Period */
    private $period;

    /** @var Project */
    private $project;

    public function __construct(Period $period)
    {
        $this->period = $period;
        $this->project = $period->project;
    }

    function run()
    {
        $key = "project-info-{$this->project->id}-{$this->period->id}";
        if (request()->exists('clear') || request()->exists('refresh')) {
            \Cache::forget($key);
        }

        return \Cache::remember($key, Carbon::now()->addDay(), function () {
            return $this->getInfo();
        });
    }

    function getInfo()
    {
        $this->costSummary = MasterShadow::dashboardSummary($this->period)->get();

        if ($this->costSummary->has('MANAGEMENT RESERVE')) {
            $reserve = $this->costSummary->get('MANAGEMENT RESERVE');
            $reserve->completion_cost = $reserve->remaining_cost = 0;
            $reserve->completion_cost_var = $reserve->budget_cost;

            $progress = min(1, $this->costSummary->sum('to_date_cost') / $this->cost_summary->sum('budget_cost'));
            $reserve->to_date_var = $reserve->allowable_cost = $progress * $reserve->budget_cost;
        }

        $this->wasteIndexTrend = $this->project->periods()->latest('id')
            ->where('global_period_id', '>=', 12)
            ->readyForReporting()->take(6)->get()->reverse()->keyBy('id')->map(function ($period) {
                $report = new WasteIndexReport($period);
                $data = $report->run();

                $p = new Fluent(['name' => $period->name, 'value' => abs($data['total_pw_index'])]);
                return $p;
            });

        $this->wasteIndex = $this->wasteIndexTrend->get($this->period->id)->value;

        $this->productivityIndexTrend = $this->getProductivityIndexTrend();

        $this->cpiTrend = MasterShadow::where('master_shadows.project_id', $this->project->id)
            ->cpiTrendChart()->get()->reverse()->map(function ($item) {
                $item->value = round($item->value, 4);
                return $item;
            });

        $this->spiTrend = $this->project->periods()
            ->where('status', Period::GENERATED)
            ->where('global_period_id', '>=', 12)
            ->take(6)
            ->latest('id')->get(['name', 'spi_index'])->reverse();

        $cost = MasterShadow::where('period_id', $this->period->id)
            ->selectRaw('sum(to_date_cost) actual_cost, sum(remaining_cost) remaining_cost')->first();

        $completion_cost = $cost->actual_cost + $cost->remaining_cost;

        $this->actual_cost_percentage = round($cost->actual_cost * 100 / $completion_cost, 2);
        $this->remaining_cost_percentage = round($cost->remaining_cost * 100 / $completion_cost, 2);

        $this->actualRevenue = $this->getActualRevenue();

        return [
            'project' => $this->project,
            'costSummary' => $this->costSummary,
            'period' => $this->period,
            'periods' => Period::where(['project_id' => $this->project->id])->readyForReporting()->get(),
            'cpiTrend' => $this->cpiTrend,
            'spiTrend' => $this->spiTrend,
            'wasteIndex' => $this->wasteIndex,
            'wasteIndexTrend' => $this->wasteIndexTrend,
            'productivityIndexTrend' => $this->productivityIndexTrend,
            'actual_cost' => $cost->actual_cost,
            'actual_cost_percentage' => $this->actual_cost_percentage,
            'remaining_cost' => $cost->remaining_cost,
            'remaining_cost_percentage' => $this->remaining_cost_percentage,
            'actualRevenue' => $this->actualRevenue,
            'budgetInfo' => $this->getBudgetInfo(),
            'costInfo' => $this->getCostInfo()
        ];
    }

    function excel()
    {

    }

    private function getProductivityIndexTrend()
    {
        $periods = $this->project->periods()
            ->where('status', Period::GENERATED)
            ->where('global_period_id', '>=', 12)->take(6)->pluck('name', 'id');

        $allowable_qty = MasterShadow::whereIn('period_id', $periods->keys())
            ->where('resource_type_id', 2)
            ->groupBy('period_id')->selectRaw('period_id, sum(allowable_qty) as allowable_qty')->get();

        $cost_man_days = CostManDay::whereIn('period_id', $periods->keys()->toArray())
            ->selectRaw('period_id, sum(actual) as actual')
            ->groupBy('period_id')->get()->keyBy('period_id');

        return $allowable_qty->map(function ($period) use ($cost_man_days, $periods) {
            $actual = $cost_man_days->get($period->period_id)->actual ?? 0;
            $value = 0;
            if ($actual) {
                $value = $period->allowable_qty / $actual;
            }

            $name = $periods->get($period->period_id, '');

            return new Fluent(compact('name', 'value'));
        });
    }

    private function getActualRevenue()
    {
        $periods = $this->project->periods->pluck('name', 'id');
        return ActualRevenue::where('project_id', $this->project->id)
            ->selectRaw('period_id, sum(value) as value')
            ->groupBy('period_id')->get()->map(function ($period) use ($periods) {
                return new Fluent([
                    'name' => $periods->get($period->period_id, ''),
                    'value' => round($period->value, 2)
                ]);
            });
    }

    private function getBudgetInfo()
    {
        $first = BudgetRevision::where('project_id', $this->project->id)->orderBy('id')->first();
        $revision0 = [];

        if ($first) {
            $revision0['budget_cost'] = RevisionBreakdownResourceShadow::where('revision_id', $first->id)->sum('budget_cost');
            $revision0['revised_contract_amount'] = $first->revised_contract_amount;
            $revision0['general_requirements'] = RevisionBreakdownResourceShadow::where('revision_id', $first->id)
                ->where('resource_type_id', 1)->sum('budget_cost');
            $revision0['management_reserve'] = RevisionBreakdownResourceShadow::where('revision_id', $first->id)
                ->where('resource_type_id', 8)->sum('budget_cost');
            $revision0['eac_contract_amount'] = $first->eac_contract_amount;
            $revision0['profit'] = $first->planned_profit_amount;
            $revision0['profitability_index'] = $first->planned_profitability_index;
        } else {
            $revision0['budget_cost'] = BreakDownResourceShadow::where('project_id', $this->project->id)->sum('budget_cost');
            $revision0['revised_contract_amount'] = $this->project->revised_contract_amount;
            $revision0['general_requirements'] = BreakDownResourceShadow::where('project_id', $this->project->id)
                ->where('resource_type_id', 1)->sum('budget_cost');
            $revision0['management_reserve'] = BreakDownResourceShadow::where('project_id', $this->project->id)
                ->where('resource_type_id', 8)->sum('budget_cost');
            $revision0['eac_contract_amount'] = $this->project->eac_contract_amount;
            $revision0['profit'] = $this->project->planned_profit_amount;
            $revision1['profitability_index'] = $this->project->planned_profitability;
        }

        $revision0['indirect_cost'] = $revision0['general_requirements'] + $revision0['management_reserve'];
        $revision0['direct_cost'] = $revision0['budget_cost'] - $revision0['indirect_cost'];

        $latest = BudgetRevision::where('project_id', $this->project->id)->where('is_generated', 1)->latest()->first();

        $revision1 = [];

        if ($latest) {
            $revision1['budget_cost'] = RevisionBreakdownResourceShadow::where('revision_id', $latest->id)->sum('budget_cost');
            $revision1['revised_contract_amount'] = $latest->revised_contract_amount;
            $revision1['general_requirements'] = RevisionBreakdownResourceShadow::where('revision_id', $latest->id)
                ->where('resource_type_id', 1)->sum('budget_cost');
            $revision1['management_reserve'] = RevisionBreakdownResourceShadow::where('revision_id', $latest->id)
                ->where('resource_type_id', 8)->sum('budget_cost');
            $revision1['eac_contract_amount'] = $latest->eac_contract_amount;
            $revision1['profit'] = $latest->planned_profit_amount;
            $revision1['profitability_index'] = $latest->planned_profitability_index;
        } else {
            $revision1['budget_cost'] = BreakDownResourceShadow::where('project_id', $this->project->id)->sum('budget_cost');
            $revision1['revised_contract_amount'] = $this->project->revised_contract_amount;
            $revision1['general_requirements'] = BreakDownResourceShadow::where('project_id', $this->project->id)
                ->where('resource_type_id', 1)->sum('budget_cost');
            $revision1['management_reserve'] = BreakDownResourceShadow::where('project_id', $this->project->id)
                ->where('resource_type_id', 8)->sum('budget_cost');
            $revision1['eac_contract_amount'] = $this->project->eac_contract_amount;
            $revision1['profit'] = $this->project->planned_profit_amount;
            $revision1['profitability_index'] = $this->project->planned_profitability;
        }

        $revision1['indirect_cost'] = $revision1['general_requirements'] + $revision1['management_reserve'];
        $revision1['direct_cost'] = $revision1['budget_cost'] - $revision1['indirect_cost'];

        return compact('revision0', 'revision1');
    }

    private function getCostInfo()
    {
        $info = [
            'spi' => $this->period->spi_index,
            'actual_cost' => MasterShadow::where('period_id', $this->period->id)->sum('to_date_cost'),
            'allowable_cost' => MasterShadow::where('period_id', $this->period->id)->sum('allowable_ev_cost'),
        ];

        $budget_cost = MasterShadow::where('period_id', $this->period->id)->sum('budget_cost');

        $info['variance'] = $info['allowable_cost'] - $info['actual_cost'];
        $info['cpi'] = $info['allowable_cost'] / $info['actual_cost'];
        $info['cost_progress'] = $info['actual_cost'] * 100 / $budget_cost;
        $info['waste_index'] = $this->wasteIndex ?? 0;
        $info['time_progress'] = $this->period->actual_progress;
        $info['productivity_index'] = $this->productivityIndexTrend->where('period_id', $this->period->id)->value ?? 0;
        $info['actual_start_date'] = $this->project->actual_start_date;

        return $info;
    }
}