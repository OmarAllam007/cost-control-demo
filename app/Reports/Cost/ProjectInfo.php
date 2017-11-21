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
        $key = "project-info-{$this->period->id}";
        if (request()->exists('clear')) {
            \Cache::forget("project-info-{$this->period->id}");
        }

        return \Cache::remember($key, Carbon::parse('+7 days'), function() {
            return $this->getInfo();
        });
    }

    function getInfo()
    {
        $summary = new CostSummary($this->period);
        $this->costSummary = $summary->run();

        $this->wasteIndex =  $query = MasterShadow::wasteIndexChart($this->project)->get()->map(function($period) {
            $period->value = round(floatval($period->value), 4);
            return $period;
        });

        $this->productivityIndexTrend = $this->getProductivityIndexTrend();

        $this->cpiTrend = MasterShadow::where('master_shadows.project_id', $this->project->id)
            ->cpiTrendChart()->get()->map(function ($item) {
                $item->value = round($item->value, 4);
                return $item;
            });

        $this->spiTrend = $this->project->periods()->readyForReporting()->get(['name', 'spi_index']);

        $cost = MasterShadow::where('period_id', $this->period->id)
            ->selectRaw('sum(to_date_cost) actual_cost, sum(remaining_cost) remaining_cost')->first();

        $this->actual_cost = round($cost->actual_cost, 2);
        $this->remaining_cost = round($cost->remaining_cost, 2);

        $this->actualRevenue = $this->getActualRevenue();

        return [
            'project' => $this->project,
            'costSummary' => $this->costSummary,
            'period' => $this->period,
            'cpiTrend' => $this->cpiTrend,
            'spiTrend' => $this->spiTrend,
            'wasteIndex' => $this->wasteIndex,
            'productivityIndexTrend' => $this->productivityIndexTrend,
            'actual_cost' => $this->actual_cost, 'remaining_cost' => $this->remaining_cost,
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
        $allowable_qty = MasterShadow::where('project_id', $this->project->id)->where('resource_type_id', 2)
            ->groupBy('period_id')->selectRaw('period_id, sum(allowable_qty) as allowable_qty')->get();

        $periods = $this->project->periods->pluck('name', 'id');

        $cost_man_days = CostManDay::whereIn('period_id', $periods->keys()->toArray())
            ->selectRaw('period_id, sum(actual) as actual')
            ->groupBy('period_id')->get()->keyBy('period_id');

        return $allowable_qty->map(function($period) use ($cost_man_days, $periods) {
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
            ->groupBy('period_id')->get()->map(function($period) use ($periods) {
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
                ->where('resource_type_id', 8)->sum('budget_cost');;
        } else {
            $revision0['budget_cost'] = BreakDownResourceShadow::where('project_id', $this->project->id)->sum('budget_cost');
            $revision0['revised_contract_amount'] = $this->project->revised_contract_amount;
            $revision0['general_requirements'] = BreakDownResourceShadow::where('project_id', $this->project->id)
                ->where('resource_type_id', 1)->sum('budget_cost');;
            $revision0['management_reserve'] = BreakDownResourceShadow::where('project_id', $this->project->id)
                ->where('resource_type_id', 8)->sum('budget_cost');;
        }

        $revision0['profit'] = $revision0['revised_contract_amount'] - $revision0['budget_cost'];
        $revision0['profitability_index'] = $revision0['profit'] * 100 /  $revision0['budget_cost'];
        $revision0['indirect_cost'] = $revision0['general_requirements'] + $revision0['management_reserve'];
        $revision0['direct_cost'] = $revision0['budget_cost'] - $revision0['indirect_cost'];

        $latest = BudgetRevision::where('project_id', $this->project->id)
            ->where('is_automatic', 1)->where('is_generated', 1)
            ->latest()->first();

        $revision1 = [];

        if ($latest) {
            $revision1['budget_cost'] = RevisionBreakdownResourceShadow::where('revision_id', $latest->id)->sum('budget_cost');
            $revision1['revised_contract_amount'] = $latest->revised_contract_amount;
            $revision1['general_requirements'] = RevisionBreakdownResourceShadow::where('revision_id', $latest->id)
                ->where('resource_type_id', 1)->sum('budget_cost');
            $revision1['management_reserve'] = RevisionBreakdownResourceShadow::where('revision_id', $latest->id)
                ->where('resource_type_id', 8)->sum('budget_cost');;
        } else {
            $revision1['budget_cost'] = BreakDownResourceShadow::where('project_id', $this->project->id)->sum('budget_cost');
            $revision1['revised_contract_amount'] = $this->project->revised_contract_amount;
            $revision1['general_requirements'] = BreakDownResourceShadow::where('project_id', $this->project->id)
                ->where('resource_type_id', 1)->sum('budget_cost');;
            $revision1['management_reserve'] = BreakDownResourceShadow::where('project_id', $this->project->id)
                ->where('resource_type_id', 8)->sum('budget_cost');;
        }

        $revision1['profit'] = $revision1['revised_contract_amount'] - $revision1['budget_cost'];
        $revision1['profitability_index'] = $revision1['profit'] * 100 /  $revision1['budget_cost'];
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

        $info['variance'] = $info['actual_cost'] - $info['allowable_cost'];
        $info['cpi'] = $info['allowable_cost'] / $info['actual_cost'];
        $info['cost_progress'] = $info['actual_cost'] * 100 / $budget_cost;
        $info['waste_index'] = $this->wasteIndex->where('period_id', $this->period->id)->value ?? 0;
        $info['time_progress'] = $this->period->actual_progress;
        $info['productivity_index'] = $this->productivityIndexTrend->where('period_id', $this->period->id)->value ?? 0;
        $info['actual_start_date'] = $this->project->actual_start_date;

        return $info;
    }
}