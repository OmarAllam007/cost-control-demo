<?php
/**
 * Created by PhpStorm.
 * User: hazem
 * Date: 11/15/17
 * Time: 2:36 PM
 */

namespace App\Reports\Cost;


use App\ActualRevenue;
use App\CostManDay;
use App\MasterShadow;
use App\Period;
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
        return \Cache::remember("project-info-{$this->period->id}", Carbon::parse('+7 days'), function() {
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

            $this->spiTrend = $this->project->periods()->get(['name', 'spi_index']);

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
                'actualRevenue' => $this->actualRevenue
            ];
        });
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
}