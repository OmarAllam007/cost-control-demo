<?php

namespace App\Reports\Budget;

use App\Boq;
use App\BreakDownResourceShadow;
use App\Project;
use Illuminate\Support\Collection;
use Illuminate\Support\Fluent;
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
        $boqs = Boq::where('project_id', $this->project->id)->get(['id', 'type', 'quantity', 'dry_ur']);
        $budgets = BreakDownResourceShadow::groupBy('boq_id')
            ->selectRaw('boq_id, sum(budget_cost) as cost, AVG(budget_qty) as budget_qty, count(DISTINCT wbs_id) as wbs_count')
            ->get()->keyBy('boq_id');

        $disciplines = [];

        foreach ($boqs as $boq) {
            $type = strtoupper($boq->type);
            if (empty($disciplines[$type])) {
                $disciplines[$type] = ['cost_diff' => 0, 'qty_diff' => 0, 'type' => $type];
            }

            $budget = $budgets->get($boq->id);

            if ($budget) {
                $qty = $budget->budget_qty * $budget->wbs_count;
                $price = $qty? $budget->cost / $qty : 0;
                $disciplines[$type]['cost_diff'] += ($price - $boq->dry_ur) * $qty;
                $disciplines[$type]['qty_diff'] += ($qty - $boq->quantity) * $price;
            }
        }

        $this->disciplines = collect($disciplines)->sortBy('type');

        return ['project' => $this->project, 'disciplines' => $this->disciplines];
    }

    function excel()
    {
        $writer = \Excel::create(slug($this->project->name), function (LaravelExcelWriter $writer) {
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
                $cost['type'], $cost['cost_diff'], $cost['qty_diff']
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