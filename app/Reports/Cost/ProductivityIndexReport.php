<?php

namespace App\Reports\Cost;

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

    public function __construct(Period $period)
    {
        $this->period = $period;
        $this->project = $period->project;
    }

    function run()
    {
        $this->wbs_levels = $this->project->wbs_levels->groupBy('parent_id');

        $this->activities = MasterShadow::where('period_id', $this->period_id->id)
            ->where('resource_type_id', 2)->where('to_date_cost', '>', 0)
            ->selectRaw("wbs_id, activity_id, activity, sum(budget_unit) as budget_unit, avg(progress) as progress")
            ->selectRaw("sum(allowable_qty) as allowable_qty")
            ->groupBy(['wbs_id', 'activity_id', 'activity'])
            ->get()->groupBy('wbs_id')->map(function($group) {
                return $group->groupBy('activity_id');
            });

        $this->actual_man_days = CostManDays::where('period_id', $this->period->id)
            ->selectRaw()->groupBy(['wbs_id', 'activity_id'])
            ->get()->groupBy(function($activity) {
                return $activity->wbs_id . '.' . $activity->id;
            });

        $this->tree = $this->buildTree();

        return ['project' => $this->project, 'period' => $this->period, 'tree' => $this->tree];
    }

    protected function buildTree($parent = 0)
    {
        return $this->wbs_levels->get($parent, collect())->map(function($level){
            $level->subtree = $this->buildTree($level->id);

            $level->labour_activities = $this->activities->get($level->id)->map(function($activity) {
                $code = $activity->wbs_id . '.' . $activity->activity_id;
                $man_days = $this->actual_man_days->get($code, new Fluent());
                $activity->actual_labour_days = $man_days->actual;
                $activity->variance = $activity->allowable_qty - $activity->actual_labour_days;
                $activity->pi = $activity->allowable_qty / $activity->actual_labour_days;
                return $activity;
            });

            $level->progress = $level->subtree->flatten(1)->avg('progress');
            $level->budget_unit = $level->subtree->sum('budget_unit') + $level->labour_activities->sum('budget_unit');
            $level->actual_labour_days = $level->subtree->sum('actual_labour_days') + $level->labour_activities->sum('actual_labour_days');
            $level->allowable_qty = $level->subtree->sum('allowable_qty') + $level->labour_activities->sum('allowable_qty');
            $level->variance = $level->subtree->sum('variance') + $level->labour_activities->sum('variance');
            $level->pi = $level->allowable_qty / $level->actual_labour_days;

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