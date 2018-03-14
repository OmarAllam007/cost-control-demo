<?php

namespace App\Reports\Cost;

use App\MasterShadow;
use App\Period;
use App\Project;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Classes\LaravelExcelWorksheet;
use Maatwebsite\Excel\Readers\LaravelExcelReader;
use Maatwebsite\Excel\Writers\CellWriter;

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
    private $row = 11;

    function __construct(Period $period)
    {
        $this->period = $period;
        $this->project = $period->project;
        $this->threshold = request('threshold', $this->project->cost_threshold);
        $this->threshold_value = request('threshold_value', 0);
    }

    function run()
    {
        $this->wbs_levels = $this->project->wbs_levels->groupBy('parent_id');

        $this->activities = $this->applyFilters(
            MasterShadow::where('period_id', $this->period->id)
                ->where('to_date_qty', '>', 0)
                ->selectRaw('wbs_id, activity, sum(allowable_ev_cost) as allowable_cost, sum(to_date_cost) as to_date_cost')
                ->selectRaw('sum(allowable_ev_cost) - sum(to_date_cost) as variance')
                ->selectRaw('((sum(to_date_cost) - sum(allowable_ev_cost)) * 100 / sum(allowable_ev_cost)) as increase')
                ->groupBy('wbs_id', 'activity')
                ->orderBy('wbs_id', 'activity')
        )->get()->groupBy('wbs_id');

        $this->tree = $this->buildTree();

        $periods = $this->project->periods()->readyForReporting()->orderBy('id', 'DESC')->pluck('name', 'id');

        return [
            'project' => $this->project, 'period' => $this->period, 'tree' => $this->tree, 
            'threshold' => $this->threshold, 'periods' => $periods, 'threshold_value' => $this->threshold_value,
        ];
    }

    protected function buildTree($parent = 0)
    {
        return $this->wbs_levels->get($parent, collect())->map(function($level) {
            $level->subtree = $this->buildTree($level->id);

            $level->activities = $this->activities->get($level->id, collect())->filter(function($activity) {
                $activity->compare_variance = - $activity->variance;
                if ($this->threshold >= 0) {
                    $threshold = $activity->increase > $this->threshold;
                } else {
                    $threshold = $activity->increase < $this->threshold;
                }

                if ($this->threshold_value >= 0) {
                    $threshold_value = abs($activity->variance) > abs($this->threshold_value);
                } else {
                    $threshold_value = $activity->variance > abs($this->threshold_value);
                }

                return $threshold && $threshold_value;
            });

            $level->to_date_cost = $level->subtree->sum('to_date_cost') + $level->activities->sum('to_date_cost');
            $level->allowable_cost = $level->subtree->sum('allowable_cost') + $level->activities->sum('allowable_cost');
            $level->variance = $level->allowable_cost - $level->to_date_cost;
            $level->compare_variance = $level->to_date_cost - $level->allowable_cost;
            $level->increase = 0;
            if ($level->allowable_cost) {
                $level->increase = $level->compare_variance * 100 / $level->allowable_cost;
            }

            return $level;
        })->reject(function ($level) {
            return ($level->subtree->isEmpty() && $level->activities->isEmpty());
        });
    }

    function excel()
    {
        return \Excel::load(storage_path('templates/cost-threshold.xlsx'), function(LaravelExcelReader $excel) {
            $excel->sheet(0, function($sheet) {
                $this->sheet($sheet);
            });

            $excel->setFilename(slug($this->project->name) . '-cost_threshold');
            $excel->export('xlsx');
        });
    }

    function sheet(LaravelExcelWorksheet $sheet)
    {
        $this->run();

        $sheet->setCellValue('A4', "Project: {$this->project->name}");
        $sheet->setCellValue('A5', "Issue Date: " . date('d M Y'));
        $sheet->setCellValue('A6', "Period: {$this->period->name}");
        $sheet->setCellValue('A7', "Threshold: {$this->threshold}%");

        $this->tree->each(function ($level) use ($sheet) {
            $this->buildExcelLevel($sheet, $level);
        });
    }

    private function buildExcelLevel(LaravelExcelWorksheet $sheet, $level, $depth = 0)
    {
        $sheet->row(++$this->row, [
            $level->name, '', $level->allowable_cost, $level->to_date_cost, $level->variance, $level->increase / 100
        ]);

        if ($depth) {
            $sheet->getRowDimension($this->row)
                ->setOutlineLevel(min($depth, 7))
                ->setCollapsed(true)->setVisible(false);

            $sheet->cells("A{$this->row}", function(CellWriter $cells) use ($depth) {
                $cells->setTextIndent(4 * $depth);
            });
        }

        $sheet->cells("A{$this->row}:F{$this->row}", function (CellWriter $cells) {
            $cells->setFont(['bold' => true])
                ->setBackground('#d9edf7')
                ->setBorder(false, 'thin', 'thin', false);
        });

        $level->subtree->each(function($sublevel) use ($sheet, $depth) {
            $this->buildExcelLevel($sheet, $sublevel, $depth + 1);
        });

        ++$depth;
        $level->activities->each(function($activity) use ($sheet, $depth) {
            $sheet->row(++$this->row, [
                '', $activity->activity, $activity->allowable_cost, $activity->to_date_cost, $activity->variance, $activity->increase / 100
            ]);

            $sheet->getRowDimension($this->row)
                ->setOutlineLevel(min($depth, 7))
                ->setCollapsed(true)->setVisible(false);
        });
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