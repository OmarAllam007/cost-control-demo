<?php

namespace App\Reports\Budget;

use App\BreakDownResourceShadow;
use App\Project;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Classes\LaravelExcelWorksheet;
use Maatwebsite\Excel\Writers\CellWriter;
use Maatwebsite\Excel\Writers\LaravelExcelWriter;

class BudgetCostByResourceTypeReport
{
    /**
     * @var Project
     */
    private $project;

    /** @var Collection */
    protected $costs;

    /** @var int */
    protected $row = 2;

    public function __construct(Project $project)
    {
        $this->project = $project;
    }

    function run()
    {
        $costs = BreakDownResourceShadow::whereProjectId($this->project->id)->budgetOnly()->from('break_down_resource_shadows as sh')
            ->selectRaw('resource_type as resource_type, sum(budget_cost) as budget_cost')
            ->groupBy('resource_type')->orderBy('resource_type')
            ->get();

        $total_cost = $costs->sum('budget_cost');
        $this->costs = $costs->map(function($cost) use ($total_cost) {
            $cost->weight = $total_cost? $cost->budget_cost * 100 / $total_cost : 0;
            return $cost;
        });

        return ['project' => $this->project, 'costs' => $this->costs, 'total_cost' => $total_cost];
    }

    function excel()
    {

        \Excel::create(slug($this->project->name) . '-budget_cost_by_resource_type', function (LaravelExcelWriter $excel){
            $excel->sheet('Budget Cost by Resource Type', function (LaravelExcelWorksheet $sheet) {
                $this->sheet($sheet);
            });

            $excel->download('xlsx');
        });
    }

    public function sheet($sheet)
    {
        $data = $this->run();

        $sheet->row(1, ['Resource Type', 'Budget Cost', 'Weight']);


        $this->costs->each(function ($cost) use ($sheet) {
            $sheet->row($this->row, [$cost->resource_type, $cost->budget_cost, $cost->weight / 100]);
            ++$this->row;
        });

        $sheet->row($this->row, ['Total', $data['total_cost'], 1]);

        $sheet->cells('A1:C1', function (CellWriter $cells) {
            $cells->setFont(['bold' => true])->setBackground('#3f6caf')->setFontColor('#ffffff');
        });

        $sheet->cells("A{$this->row}:C{$this->row}", function (CellWriter $cells) {
            $cells->setFont(['bold' => true])->setBackground('#3f6caf')->setFontColor('#ffffff');
        });


        $sheet->getColumnDimension('A')->setWidth(50);
        $sheet->setAutoSize(['B', "C"]);
        $sheet->setAutoSize(false);

        $sheet->setColumnFormat(["B2:B{$this->row}" => '#,##0.00']);
        $sheet->setColumnFormat(["C2:C{$this->row}" => '0.00%']);
    }

}