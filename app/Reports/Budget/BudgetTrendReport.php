<?php

namespace App\Reports\Budget;

use App\Project;
use App\Revision\RevisionBreakdownResourceShadow;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Classes\LaravelExcelWorksheet;
use Maatwebsite\Excel\Writers\LaravelExcelWriter;

class BudgetTrendReport
{
    /** @var Project */
    protected $project;

    /** @var Collection */
    protected $revisions;

    /** @var Collection */
    protected $data;

    /** @var Collection */
    protected $disciplineTotals;

    /** @var Collection  */
    protected $activityTotals;

    /** @var int */
    protected $row = 1;

    function __construct(Project $project)
    {
        $this->project = $project;

        $this->data = collect();
        $this->disciplineTotals = collect();
        $this->activityTotals = collect();
    }

    function run()
    {
        $this->revisions = $this->project->revisions()->pluck('name', 'id');

        $result = RevisionBreakdownResourceShadow::trendReport($this->project)->get();

        if ($result) {
            $this->disciplineTotals = RevisionBreakdownResourceShadow::disciplineTotals($this->project)->get()->groupBy('discipline')
                ->map(function(Collection $group) {
                    return $group->keyBy('revision_id');
                });

            $this->activityTotals = RevisionBreakdownResourceShadow::activityTotals($this->project)->get()->groupBy('activity')
                ->map(function(Collection $group) {
                    return $group->keyBy('revision_id');
                });

            $this->data = $result->groupBy('discipline')->map(function(Collection $group){
                return $group->groupBy('activity')->map(function(Collection $group) {
                    return $group->groupBy('resource_name')->filter(function($resources){
                        $costs = $resources->pluck('cost');
                        $firstCost = $costs->first();
                        foreach ($costs as $cost) {
                            if ($cost != $firstCost) {
                                return true;
                            }
                        }
                        return false;
                    })->map(function (Collection $group) {
                        return $group->keyBy('revision_id');
                    });
                })->filter(function ($activity) {
                    return $activity->count();
                });
            });
        }

        return [
            'project' => $this->project, 'revisions' => $this->revisions, 'data'=>$this->data,
            'disciplineTotals' => $this->disciplineTotals, 'activityTotals' => $this->activityTotals
        ];
    }

    function excel()
    {
        return \Excel::create(slug($this->project->name) . '-budget_trend', function (LaravelExcelWriter $writer) {
            $writer->sheet('Budget Trend', function(LaravelExcelWorksheet $sheet) {
                $this->sheet($sheet);
            });

            $writer->download('xlsx');
        });
    }

    function sheet(LaravelExcelWorksheet $sheet)
    {
        $this->run();

        $sheet->setCellValue("A1",'Activity');
        $ord = ord('A');

        foreach($this->revisions as $rev_name) {
            $char = chr(++$ord);
            $sheet->setCellValue("{$char}1", $rev_name);
        }

        $char = chr(++$ord);
        $sheet->setCellValue("{$char}1", 'Difference');

        $char = chr(++$ord);
        $sheet->setCellValue("{$char}1", '% Difference');

        foreach($this->data as $discipline => $disciplineData) {
            ++$this->row;
            $sheet->setCellValue("A{$this->row}", $discipline);

            foreach ($disciplineData as $activity => $activityData) {
                ++$this->row;
                $sheet->setCellValue("A{$this->row}", $activity);
            }
        }

    }
}