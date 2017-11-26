<?php

namespace App\Reports\Cost;

use App\MasterShadow;
use App\Period;
use App\Project;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Classes\LaravelExcelWorksheet;

class ThresholdReport
{
    /** @var Period */
    private $period;

    /** @var Project */
    private $project;

    /** @var  Collection */
    private $activities;

    /** @var  Collection */
    private $wbs_levels;

    /** @var Collection */
    private $tree;

    /** @var int */
    private $row = 12;

    function __construct(Period $period)
    {
        $this->period = $period;
        $this->project = $period->project;
        $this->threshold = request('threshold', $this->project->cost_threshold);
    }

    function run()
    {
        $this->wbs_levels = $this->project->wbs_levels->groupBy('parent_id');

        $this->activities = $this->applyFilters(
            MasterShadow::where('period_id', $this->period->id)
                ->selectRaw('wbs_id, activity, sum(allowable_ev_cost) as allowable_cost, sum(to_date_cost) as to_date_cost')
                ->selectRaw('sum(to_date_cost) - sum(allowable_ev_cost) as variance')
                ->selectRaw('((sum(to_date_cost) - sum(allowable_ev_cost)) * 100 / sum(allowable_ev_cost)) as increase')
                ->groupBy('wbs_id', 'activity')
                ->orderBy('wbs_id', 'activity')
        )->get()->groupBy('wbs_id');

        $this->tree = $this->buildTree()->reject(function ($level) {
            return ($level->subtree->isEmpty() && $level->activities->isEmpty()) || $level->variance <= 0;
        });

        $periods = $this->project->periods()->readyForReporting()->orderBy('id', 'DESC')->pluck('name', 'id');

        return [
            'project' => $this->project, 'period' => $this->period, 'tree' => $this->tree, 
            'threshold' => $this->threshold, 'periods' => $periods
        ];
    }

    protected function buildTree($parent = 0)
    {
        return $this->wbs_levels->get($parent, collect())->map(function($level) {
            $level->subtree = $this->buildTree($level->id);
            $level->activities = $this->activities->get($level->id, collect());

            $level->to_date_cost = $level->subtree->sum('to_date_cost') + $level->activities->sum('to_date_cost');
            $level->allowable_cost = $level->subtree->sum('allowable_cost') + $level->activities->sum('allowable_cost');
            $level->variance = $level->to_date_cost - $level->allowable_cost;
            $level->increase = 0;
            if ($level->allowable_cost) {
                $level->increase = $level->variance * 100 / $level->allowable_cost;
            }
            
            $level->activities = $level->activities->reject(function($activity) {
                return $activity->increase < $this->threshold;
            });

            $level->subtree = $level->subtree->reject(function ($level) {
                return ($level->subtree->isEmpty() && $level->activities->isEmpty()) || $level->variance <= 0;
            });

            return $level;
        });
    }

    function excel()
    {
        return \Excel::load(storage_path('templates/threshold_report'), function() {

        });
    }

    function sheet(LaravelExcelWorksheet $sheet)
    {
        $this->run();
    }

    private function applyFilters(Builder $query)
    {
        $activity = request('activity', []);
        if ($activity) {
            $query->whereIn('activity_id', $activity);
        }

        return $query;
    }
}