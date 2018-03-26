<?php

namespace App\Reports\Budget;

use App\Project;
use App\Revision\RevisionBreakdownResourceShadow;
use function compact;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Classes\LaravelExcelWorksheet;
use Maatwebsite\Excel\Writers\LaravelExcelWriter;
use PHPExcel_Style_Color;
use function range;

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

    /** @var Collection */
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
                ->map(function (Collection $group) {
                    return $group->keyBy('revision_id');
                });

            $this->activityTotals = RevisionBreakdownResourceShadow::activityTotals($this->project)->get()->groupBy('activity')
                ->map(function (Collection $group) {
                    return $group->keyBy('revision_id');
                });

            $this->data = $result->groupBy('discipline')->map(function (Collection $group) {
                return $group->groupBy('activity')->map(function (Collection $group) {
                    return $group->groupBy('resource_name')->filter(function ($resources) {
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
            'project' => $this->project, 'revisions' => $this->revisions, 'data' => $this->data,
            'disciplineTotals' => $this->disciplineTotals, 'activityTotals' => $this->activityTotals
        ];
    }

    function excel()
    {
        return \Excel::create(slug($this->project->name) . '-budget_trend', function (LaravelExcelWriter $writer) {
            $writer->sheet('Budget Trend', function (LaravelExcelWorksheet $sheet) {
                $this->sheet($sheet);
            });

            $writer->download('xlsx');
        });
    }

    function sheet(LaravelExcelWorksheet $sheet)
    {
        $this->run();

        $sheet->setCellValue("A1", 'Activity');
        $ord = ord('A');

        foreach ($this->revisions as $rev_name) {
            $char = chr(++$ord);
            $sheet->setCellValue("{$char}1", $rev_name);
        }

        $char = chr(++$ord);
        $sheet->setCellValue("{$char}1", 'Difference');

        $char = chr(++$ord);
        $sheet->setCellValue("{$char}1", '% Difference');

        $sheet->getStyle("A1:{$char}1")->applyFromArray([
            'fill' => [
                'type' => 'solid', 'startcolor' => ['rgb' => '2779BD'], 'endcolor' => ['rgb' => '2779BD']
            ],
            'font' => ['bold' => true, 'color' => ['rgb' => 'EFF8FF']]
        ]);

        $disciplineStyle = [
            'fill' => ['type' => 'solid', 'startcolor' => ['rgb' => 'BCDEFA'], 'endcolor' => ['rgb' => 'BCDEFA']],
            'font' => ['bold' => true]
        ];

        $activityStyle = [
            'fill' => ['type' => 'solid', 'startcolor' => ['rgb' => 'EFF8FF'], 'endcolor' => ['rgb' => 'EFF8FF']],
            'font' => ['bold' => true]
        ];

        $ord = ord('A') + $this->revisions->count() + 2;
        $char = chr($ord);
        foreach ($this->data as $discipline => $disciplineData) {
            ++$this->row;
            $sheet->setCellValue("A{$this->row}", $discipline);
            $sheet->getStyle("A{$this->row}:{$char}{$this->row}")->applyFromArray($disciplineStyle);
            $sums = $this->disciplineTotals->get($discipline)->sortBy('revision_id')->groupBy('revision_id')->map(function($group) {
                return $group->sum('cost');
            });

            $ord = ord('A');
            foreach ($this->revisions as $rev_id => $name) {
                $chr = chr(++$ord);
                $sheet->setCellValue("{$chr}{$this->row}", $sums->get($rev_id)?: '0.00');
            }

            $chr = chr(++$ord);
            $diff = ($sums->first() - $sums->last());
            $sheet->setCellValue("{$chr}{$this->row}", $diff?: '0.00');
            $chr = chr(++$ord);
            $sheet->setCellValue("{$chr}{$this->row}", ($diff / ($sums->first()?: 0.001))?: '0.00');

            foreach ($disciplineData as $activity => $activityData) {
                ++$this->row;
                $sheet->setCellValue("A{$this->row}", $activity);
                $sheet->getStyle("A{$this->row}")->getAlignment()->setIndent(4);

                $sums = $this->activityTotals->get($activity)->sortBy('revision_id')->groupBy('revision_id')->map(function($group) {
                    return $group->sum('cost');
                });

                $ord = ord('A');
                foreach ($this->revisions as $rev_id => $name) {
                    $chr = chr(++$ord);
                    $sheet->setCellValue("{$chr}{$this->row}", $sums->get($rev_id)?: '0.00');
                }

                $chr = chr(++$ord);
                $diff = ($sums->first() - $sums->last());
                $sheet->setCellValue("{$chr}{$this->row}", $diff?: '0.00');
                $chr = chr(++$ord);
                $sheet->setCellValue("{$chr}{$this->row}", ($diff / ($sums->first()?: 0.001))?: '0.00');

                $sheet->getRowDimension($this->row)->setOutlineLevel(1)->setVisible(false)->setCollapsed(true);
                $sheet->getStyle("A{$this->row}:{$char}{$this->row}")->applyFromArray($activityStyle);
                foreach ($activityData as $resource => $resourcesData) {
                    ++$this->row;
                    $sheet->setCellValue("A{$this->row}", $resource);
                    $sheet->getRowDimension($this->row)->setOutlineLevel(2)->setVisible(false)->setCollapsed(true);
                    $sheet->getStyle("A{$this->row}")->getAlignment()->setIndent(8);
                    $ord = ord('A');

                    $ord = ord('A');
                    $costs = $resourcesData->sortBy('revision_id')->pluck('cost', 'revision_id');
                    foreach ($this->revisions as $rev_id => $name) {
                        $chr = chr(++$ord);
                        $sheet->setCellValue("{$chr}{$this->row}", $costs->get($rev_id)?: '0.00');
                    }

                    $chr = chr(++$ord);
                    $diff = ($costs->first() - $costs->last());
                    $sheet->setCellValue("{$chr}{$this->row}", $diff?: '0.00');
                    $chr = chr(++$ord);
                    $sheet->setCellValue("{$chr}{$this->row}", ($diff / ($costs->first()?: 0.001))?: '0.00');
                }
            }
        }

        $sheet->getStyle("B2:{$char}{$this->row}")->getNumberFormat()->setBuiltInFormatCode(40);
        $sheet->getStyle("{$char}2:{$char}{$this->row}")->getNumberFormat()->setBuiltInFormatCode(10);

        $sheet->setAutoSize(range('B', $char));

        $sheet->getColumnDimension('A')->setWidth(80)->setAutoSize(false);
        $sheet->setShowSummaryBelow(false);
        $sheet->setTitle('Budget Trend');
        $sheet->setAutoSize(false);

    }
}