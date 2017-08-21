<?php

namespace App\Reports\Budget;

use App\BreakDownResourceShadow;
use App\Project;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Classes\LaravelExcelWorksheet;
use Maatwebsite\Excel\Writers\CellWriter;
use Maatwebsite\Excel\Writers\LaravelExcelWriter;

class BudgetCostByDisciplineReport
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
        $costs = BreakDownResourceShadow::whereProjectId($this->project->id)->from('break_down_resource_shadows as sh')
            ->leftJoin('std_activities as stda', 'sh.activity_id' , '=' ,'stda.id')
            ->selectRaw('stda.discipline as discipline, sum(budget_cost) as budget_cost')
            ->groupBy('stda.discipline')->orderBy('stda.discipline')
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

        \Excel::create(slug($this->project->name) . '-budget_cost_by_discipline', function (LaravelExcelWriter $excel){
            $excel->sheet('Budget Cost by Discipline', function (LaravelExcelWorksheet $sheet) {
                $data = $this->run();

                $sheet->row(1, ['Discipline', 'Budget Cost', 'Weight']);


                $this->costs->each(function ($cost) use ($sheet) {
                    $sheet->row($this->row, [$cost->discipline, $cost->budget_cost, $cost->weight / 100]);
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
            });

            $excel->download('xlsx');
        });
    }
}