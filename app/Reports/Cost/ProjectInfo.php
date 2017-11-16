<?php
/**
 * Created by PhpStorm.
 * User: hazem
 * Date: 11/15/17
 * Time: 2:36 PM
 */

namespace App\Reports\Cost;


use App\MasterShadow;
use App\Period;

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
        $summary = new CostSummary($this->period);
        $this->costSummary = $summary->run();

        $this->wasteIndex =  $query = MasterShadow::from('master_shadows as sh')
            ->where('sh.project_id', $this->project->id)
            ->where('sh.to_date_cost', '>', 0)
            ->where('sh.resource_type_id', 3)
            ->join('periods as p', 'sh.period_id', '=', 'p.id')
            ->select(['sh.period_id'])
            ->selectRaw('p.name as p_name, ((sum(sh.allowable_ev_cost) - sum(sh.to_date_cost)) * 100 / sum(sh.allowable_ev_cost)) as value')
            ->groupBy('sh.period_id', 'p.name')->get()->map(function($period) {
                $period->value = round(floatval($period->value), 4);
                return $period;
            });

        $this->cpiTrend = MasterShadow::where('master_shadows.project_id', $this->project->id)
            ->cpiTrendChart()->get()->map(function ($item) {
                $item->value = round($item->value, 4);
                return $item;
            });

        return [
            'project' => $this->project,
            'costSummary' => $this->costSummary,
            'period' => $this->period,
            'cpiTrend' => $this->cpiTrend,
            'wasteIndex' => $this->wasteIndex
        ];
    }

    function excel()
    {

    }
}