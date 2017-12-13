<?php
/**
 * Created by PhpStorm.
 * User: omar
 * Date: 27/12/16
 * Time: 11:19 ص
 */

namespace App\Http\Controllers\Reports\CostReports;

use App\MasterShadow;
use App\Period;
use App\Project;
use App\WbsLevel;
use Illuminate\Support\Collection;
use Illuminate\Support\Fluent;
use Maatwebsite\Excel\Classes\LaravelExcelWorksheet;
use Maatwebsite\Excel\Classes\PHPExcel;
use Maatwebsite\Excel\Readers\LaravelExcelReader;
use Maatwebsite\Excel\Writers\LaravelExcelWriter;

class OverdraftReport
{

    /** @var Project */
    protected $project;

    /** @var Collection  */
    protected $wbs_levels;

    /** @var Collection  */
    protected $rawData;

    /** @var Collection  */
    protected $tree;

    /** @var Fluent */
    private $totals;


    /** @var int */
    private $row = 10;

    public function __construct(Period $period)
    {
        $this->period = $period;
        $this->project = $period->project;
    }

    public function run()
    {
        $this->wbs_levels = $this->project->wbs_levels->groupBy('parent_id');
        $this->rawData = MasterShadow::overDraftReport($this->period)->get()->groupBy('wbs_id');;

        $this->tree = $this->buildTree();

        $periods = $this->project->periods()->readyForReporting()->pluck('name', 'id');

        $this->totals = new Fluent([
            'physical_revenue' => $this->tree->sum('physical_revenue'),
            'physical_revenue_upv' => $this->tree->sum('physical_revenue_upv'),
            'actual_revenue' => $this->tree->sum('actual_revenue'),
            'var' => $this->tree->sum('var'),
            'var_upv' => $this->tree->sum('var_upv'),
        ]);

        return [
            'tree' => $this->tree,
            'period' => $this->period,
            'project' => $this->project,
            'periods' => $periods,
            'totals' => $this->totals
        ];
    }

    protected function buildTree($parent = 0)
    {

        return $this->wbs_levels->get($parent, collect())->map(function($level) {
            $level->subtree = $this->buildTree($level->id);
            $level->boqs = $this->rawData->get($level->id, collect())->map(function ($boq) {
                $boq->var = $boq->actual_revenue - $boq->physical_revenue;
                $boq->var_upv = $boq->actual_revenue - $boq->physical_revenue_upv;
                return $boq;
            });

            $level->var = $level->subtree->sum('var') + $level->boqs->sum('var');
            $level->var_upv = $level->subtree->sum('var_upv') + $level->boqs->sum('var_upv');
            $level->physical_revenue = $level->subtree->sum('physical_revenue') + $level->boqs->sum('physical_revenue');
            $level->physical_revenue_upv = $level->subtree->sum('physical_revenue_upv') + $level->boqs->sum('physical_revenue_upv');
            $level->actual_revenue = $level->subtree->sum('actual_revenue') + $level->boqs->sum('actual_revenue');

            return $level;
        })->reject(function($level) {
            return $level->subtree->isEmpty() && $level->boqs->isEmpty();
        });
    }

    function excel()
    {
        $excel = \PHPExcel_IOFactory::load(storage_path('templates/overdraft.xlsx'));

        $this->sheet($excel->getSheet(0));

        $filename = storage_path('app/' . uniqid('overdraft_') . '.xlsx');
        \PHPExcel_IOFactory::createWriter($excel, 'Excel2007')->save($filename);

        return \Response::download($filename,
            slug($this->project->name) . '_' . slug($this->period->name) . '_overdraft.xlsx'
        )->deleteFileAfterSend(true);
    }

    function sheet(\PHPExcel_Worksheet $sheet)
    {
        $this->run();

        $sheet->setCellValue('A4', 'Project: ' . $this->project->name);
        $sheet->setCellValue('A5', 'Period: ' . $this->period->name);
        $sheet->setCellValue('A6', 'Issue Date: ' . date('d/m/Y'));

        $this->tree->each(function($level) use ($sheet) {
            $this->buildExcelTree($sheet, $level);
        });

        return $sheet;
    }

    function buildExcelTree(\PHPExcel_Worksheet $sheet, $level, $depth = 0)
    {
        ++$this->row;
        $sheet->setCellValue("A{$this->row}", $level->name);
        $sheet->setCellValue("G{$this->row}", $level->physical_revenue);
        $sheet->setCellValue("H{$this->row}", $level->physical_revenue_upv);
        $sheet->setCellValue("I{$this->row}", $level->actual_revenue);
        $sheet->setCellValue("J{$this->row}", $level->var);
        $sheet->setCellValue("K{$this->row}", $level->var_upv);


        $sheet->getStyle("A{$this->row}")->getAlignment()->setIndent($depth);
        if ($depth) {
            $sheet->getRowDimension($this->row)
                ->setOutlineLevel(min($depth, 7))
                ->setCollapsed(true)->setVisible(false);
        }

        ++$depth;

        $level->subtree->each(function($sublevel) use ($sheet, $depth) {
            $this->buildExcelTree($sheet, $sublevel, $depth);
        });

        $level->boqs->each(function($boq) use ($sheet, $depth) {
            ++$this->row;

            $sheet->fromArray([
                $boq->description, $boq->boq_quantity, $boq->boq_unit_price,
                $boq->physical_unit, $boq->physical_unit_upv, $boq->physical_revenue,
                $boq->physical_revenue_upv, $boq->actual_revenue,
                $boq->var, $boq->var_upv,
            ], null, "A{$this->row}", true);

            $sheet->getStyle("A{$this->row}")->getAlignment()->setIndent($depth);
            $sheet->getRowDimension($this->row)
                ->setOutlineLevel(min($depth, 7))
                ->setCollapsed(true)->setVisible(false);
        });
    }
}