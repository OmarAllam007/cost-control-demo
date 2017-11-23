<?php
/**
 * Created by PhpStorm.
 * User: hazem
 * Date: 11/23/17
 * Time: 4:41 PM
 */

namespace App\Reports\Cost;


use App\MasterShadow;
use App\Period;
use App\Project;
use Maatwebsite\Excel\Classes\LaravelExcelWorksheet;

class ThresholdReport
{
    /** @var Period */
    private $period;

    /** @var Project */
    private $project;

    function __construct(Period $period)
    {
        $this->period = $period;
        $this->project = $period->project;
        $this->threshold = $this->project->cost_threshold;
    }

    function run()
    {
        $this->wbs_levels = $this->project->wbs_levels->groupBy('parent_id');

        $threshold = 1 + ($this->threshold / 100);
        $this->activities = MasterShadow::where('period_id', $this->period->id)
            ->selectRaw('wbs_id, activity, sum(allowable_ev_cost) as allowable_cost, sum(to_date_cost) as to_date_cost')
            ->selectRaw('(sum(to_date_cost) / sum(allowable_ev_cost)) as increase')
            ->groupBy('wbs_id', 'activity')
            ->having('increase', '>=', $threshold)
            ->orderBy('wbs_id', 'activity')
            ->get()->groupBy('wbs_id');

        $this->tree = $this->buildTree();
    }

    protected function buildTree($parent = 0)
    {
        return $this->wbs_levels->get($parent, collect())->map(function($level) {
            $level->subtree = $this->buildTree($level->id);
            $level->activities = $this->activities->get($level->id, collect())->map(function($activity) {

            });
            return $level;
        })->reject(function ($level) {
            return $level->subtree->isEmpty() && $level->activities->isEmpty();
        });
    }

    function excel()
    {

    }

    function sheet(LaravelExcelWorksheet $sheet)
    {
        $this->run();
    }
}