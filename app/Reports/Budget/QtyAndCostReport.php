<?php

namespace App\Reports\Budget;

use App\BreakDownResourceShadow;
use App\Project;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Classes\LaravelExcelWorksheet;
use Maatwebsite\Excel\Writers\CellWriter;
use Maatwebsite\Excel\Writers\LaravelExcelWriter;

class QtyAndCostReport
{
    /** @var Project */
    protected $project;

    /** @var Collection */
    protected $disciplines;

    /** @var int */
    protected $row = 1;

    public function __construct(Project $project)
    {
        $this->project = $project;
    }

    function run()
    {
        $this->disciplines = collect(\DB::select('SELECT type, sum((budget_price - dry_price) * budget_qty) AS cost_diff, sum((budget_qty - dry_qty) * budget_price) AS qty_diff FROM (
  SELECT concat(sh.boq_wbs_id, sh.cost_account), a.discipline AS type,
    sum(sh.boq_equivilant_rate) AS budget_price, avg(boqs.dry_ur) AS dry_price,
   avg(qs.budget_qty) AS budget_qty, avg(boqs.quantity) AS dry_qty
  FROM break_down_resource_shadows sh
    LEFT JOIN boqs ON (sh.boq_id = boqs.id)
    LEFT JOIN std_activities a ON sh.activity_id = a.id
    LEFT JOIN qty_surveys qs ON (sh.survey_id = qs.id)
  WHERE sh.project_id = 35
  GROUP BY 1, 2
) AS data GROUP BY  type'));

        return ['project' => $this->project, 'disciplines' => $this->disciplines];
    }

    function excel()
    {
        \Excel::create(slug($this->project->name), function (LaravelExcelWriter $writer) {
            $writer->sheet('Dry Vs Budget', function (LaravelExcelWorksheet $sheet) {
                $this->sheet($sheet);
            });

            $writer->download('xlsx');
        });
    }

    function sheet(LaravelExcelWorksheet $sheet)
    {
        $this->run();

        $sheet->row($this->row, [
            'Discipline', '(Budget Cost - Dry Cost) * Budget Quantity', '(Budget QTY - Dry QTY) * Budget cost'
        ]);

        $sheet->cells("A1:C1", function(CellWriter $cells) {
            $cells->setFont(['bold' => true])->setBackground('#5182bb')->setFontColor('#ffffff');
        });

        $this->disciplines->each(function($cost) use ($sheet) {
            $sheet->row(++$this->row, [
                $cost->type, $cost->cost_diff, $cost->qty_diff
            ]);
        });

        $cost_diff = $this->disciplines->sum('cost_diff');
        $qty_diff = $this->disciplines->sum('qty_diff');

        $sheet->row(++$this->row, [
            'Total', $cost_diff, $qty_diff
        ])->cells("A{$this->row}:C{$this->row}", function(CellWriter $cells) {
            $cells->setFont(['bold' => true])->setBackground('#5182bb')->setFontColor('#ffffff');
        });

        $sheet->setAutoSize(true);
        $sheet->setColumnFormat([
            "B2:C{$this->row}" => '#,##0.00_-',
        ]);

        $varCondition = new \PHPExcel_Style_Conditional();
        $varCondition->setConditionType(\PHPExcel_Style_Conditional::CONDITION_CELLIS)
            ->setOperatorType(\PHPExcel_Style_Conditional::OPERATOR_LESSTHAN)->addCondition(0)
            ->getStyle()->getFont()->getColor()->setARGB(\PHPExcel_Style_Color::COLOR_RED);
        $sheet->getStyle("B2:C{$this->row}")->setConditionalStyles([$varCondition]);

        return $sheet;
    }
}