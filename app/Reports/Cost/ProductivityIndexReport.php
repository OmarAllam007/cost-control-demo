<?php

namespace App\Reports\Cost;

use App\CostManDay;
use App\MasterShadow;
use App\Period;
use App\Project;
use Illuminate\Support\Collection;
use Illuminate\Support\Fluent;
use Maatwebsite\Excel\Classes\LaravelExcelWorksheet;

class ProductivityIndexReport
{
    /** @var Period */
    protected $period;

    /** @var Project */
    protected $project;

    /** @var Collection */
    protected $tree;

    protected $start = 12;
    protected $row = 12;

    /** @var Collection */
    protected $wbs_levels;

    /** @var Collection */
    protected $activities;

    public function __construct(Period $period)
    {
        $this->period = $period;
        $this->project = $period->project;
    }

    function run()
    {
        $this->wbs_levels = $this->project->wbs_levels->groupBy('parent_id');

        $this->activities = MasterShadow::where('period_id', $this->period->id)
            ->where('resource_type_id', 2)->where('to_date_qty', '>', 0)
            ->selectRaw("wbs_id, activity_id, activity, sum(budget_unit) as budget_unit, avg(progress) as progress")
            ->selectRaw("sum(allowable_qty) as allowable_qty")
            ->groupBy(['wbs_id', 'activity_id', 'activity'])
            ->get()->groupBy('wbs_id');

        $this->actual_man_days = CostManDay::where('period_id', $this->period->id)
            ->selectRaw('wbs_id, activity_id, sum(actual) as actual, avg(progress) as progress')
            ->groupBy(['wbs_id', 'activity_id'])
            ->get()->keyBy(function($activity) {
                return $activity->wbs_id . '.' . $activity->id;
            });

        $this->tree = $this->buildTree();

        $total_man_days = $this->tree->sum('actual_man_days');
        $this->average_pi = 0;
        if ($total_man_days) {
            $this->average_pi = $this->tree->sum('allowable_qty') / $total_man_days;
        }

        return ['project' => $this->project, 'period' => $this->period, 'tree' => $this->tree, 'average_pi' => $this->average_pi];
    }

    protected function buildTree($parent = 0)
    {
        return $this->wbs_levels->get($parent, collect())->map(function($level){
            $level->subtree = $this->buildTree($level->id);

            $level->labour_activities = $this->activities->get($level->id, collect())->map(function($activity) {
                $code = $activity->wbs_id . '.' . $activity->activity_id;
                $man_days = $this->actual_man_days->get($code, new Fluent());
                $activity->actual_man_days = $man_days->actual ?: 0;
                $activity->progress = $man_days->progress ?: 0;
                $activity->variance = $activity->allowable_qty - $activity->actual_man_days;
                $activity->pi = 0;
                if ($activity->actual_man_days) {
                    $activity->pi = $activity->allowable_qty / $activity->actual_man_days;
                }
                return $activity;
            });

            $level->budget_unit = $level->subtree->sum('budget_unit') + $level->labour_activities->sum('budget_unit');
            $level->actual_man_days = $level->subtree->sum('actual_man_days') + $level->labour_activities->sum('actual_man_days');
            $level->allowable_qty = $level->subtree->sum('allowable_qty') + $level->labour_activities->sum('allowable_qty');
            $level->variance = $level->subtree->sum('variance') + $level->labour_activities->sum('variance');
            $level->pi = 0;
            if ($level->actual_man_days) {
                $level->pi = $level->allowable_qty / $level->actual_man_days;
            }

            return $level;
        })->reject(function ($level) {
            return $level->subtree->isEmpty() && $level->labour_activities->isEmpty();
        });
    }

    function excel()
    {

    }

    function sheet(LaravelExcelWorksheet $sheet)
    {

    }

    protected function buildExcelLevel()
    {

    }
}