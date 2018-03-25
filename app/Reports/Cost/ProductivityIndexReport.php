<?php

namespace App\Reports\Cost;

use App\CostManDay;
use App\MasterShadow;
use App\Period;
use App\Project;
use Illuminate\Support\Collection;
use Illuminate\Support\Fluent;
use Maatwebsite\Excel\Classes\LaravelExcelWorksheet;
use Maatwebsite\Excel\Writers\CellWriter;
use Maatwebsite\Excel\Writers\LaravelExcelWriter;

class ProductivityIndexReport
{
    /** @var Period */
    protected $period;

    /** @var Project */
    protected $project;

    /** @var Collection */
    protected $tree;

    protected $start = 11;
    protected $row = 10;

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
            ->where('resource_type_id', 2) //->where('to_date_qty', '>', 0)
            ->selectRaw("wbs_id, activity_id, activity, sum(budget_unit) as budget_unit, avg(progress) as progress")
            ->selectRaw("sum(allowable_qty) as allowable_qty")
            ->groupBy(['wbs_id', 'activity_id', 'activity'])
            ->get()->groupBy('wbs_id');

        $this->actual_man_days = CostManDay::where('period_id', $this->period->id)
            ->selectRaw('wbs_id, activity_id, sum(actual) as actual, avg(progress) as progress')
            ->groupBy(['wbs_id', 'activity_id'])
            ->get()->keyBy(function($activity) {
                return $activity->wbs_id . '.' . $activity->activity_id;
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
        $excel = new \PHPExcel();

        $excel->removeSheetByIndex(0);
        $excel->addExternalSheet($this->sheet());

        $filename = storage_path('app/cost-summary-' . uniqid() . '.xlsx');
        $writer = new \PHPExcel_Writer_Excel2007($excel);
        $writer->setIncludeCharts(true);
        $writer->save($filename);

        $name = slug($this->project->name) . '_' . slug($this->period->name) . '_cost-summary.xlsx';
        return \Response::download($filename, $name)->deleteFileAfterSend(true);
    }

    function sheet()
    {
        $excel = \PHPExcel_IOFactory::load(storage_path('templates/productivity-index.xlsx'));
        $sheet = $excel->getSheet(0);

        $this->run();

        $sheet->setCellValue('A4', "Project: {$this->project->name}");
        $sheet->setCellValue('A5', "Issue Date: " . date('d M Y'));
        $sheet->setCellValue('A6', "Period: {$this->period->name}");

        $logo = imagecreatefrompng(public_path('images/kcc.png'));
        $drawing = new \PHPExcel_Worksheet_MemoryDrawing();
        $drawing->setName('Logo')->setImageResource($logo)
            ->setRenderingFunction(\PHPExcel_Worksheet_MemoryDrawing::RENDERING_PNG)
            ->setMimeType(\PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_PNG)
            ->setCoordinates('F2')->setWorksheet($sheet);

        $this->tree->each(function ($level) use ($sheet) {
            $this->buildExcelLevel($sheet, $level);
        });

        $sheet->setShowSummaryBelow(false);

        $sheet->getStyle("B{$this->start}:B{$this->row}")->getNumberFormat()->setBuiltInFormatCode(40);
        $sheet->getStyle("D{$this->start}:F{$this->row}")->getNumberFormat()->setBuiltInFormatCode(40);
        $sheet->getStyle("C{$this->start}:C{$this->row}")->getNumberFormat()->setBuiltInFormatCode(10);
        $sheet->getStyle("G{$this->start}:G{$this->row}")->getNumberFormat()->setBuiltInFormatCode(10);

        return $sheet;
    }

    protected function buildExcelLevel(\PHPExcel_Worksheet $sheet, $level, $depth = 0)
    {
        ++$this->row;
        $sheet->fromArray([
            $level->name, $level->budget_unit, '', $level->allowable_qty, $level->actual_man_days, $level->variance, $level->pi
        ], null, "A{$this->row}", true);

        if ($depth) {
            $sheet->getRowDimension($this->row)
                ->setOutlineLevel(min($depth, 7))
                ->setCollapsed(true)->setVisible(false);

            $sheet->getStyle("A{$this->row}")->getAlignment()->setIndent(4 * $depth);
        }

        $sheet->getStyle("A{$this->row}:G{$this->row}")->getFont()->setBold(true);
        $sheet->getStyle("A{$this->row}:G{$this->row}")->getBorders()->getAllBorders()->setBorderStyle('thin');
        $fillColor = new \PHPExcel_Style_Color();
        $fillColor->setRGB('d9edf7');
        $sheet->getStyle("A{$this->row}:G{$this->row}")
            ->getFill()->setFillType('solid')
            ->setStartColor($fillColor)->setEndColor($fillColor);

        $level->subtree->each(function($sublevel) use ($sheet, $depth) {
            $this->buildExcelLevel($sheet, $sublevel, $depth + 1);
        });

        ++$depth;
        $level->labour_activities->each(function($activity) use ($sheet, $depth) {
            ++$this->row;
            $sheet->fromArray([
                $activity->activity, $activity->budget_unit, $activity->progress / 100, $activity->allowable_qty, $activity->actual_man_days, $activity->variance, $activity->pi / 100
            ], null, "A{$this->row}", true);

            $sheet->getRowDimension($this->row)
                ->setOutlineLevel(min($depth, 7))
                ->setCollapsed(true)->setVisible(false);
        });
    }
}