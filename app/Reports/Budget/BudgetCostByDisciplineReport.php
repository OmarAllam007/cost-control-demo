<?php

namespace App\Reports\Budget;


use App\BreakDownResourceShadow;
use App\Project;
use Maatwebsite\Excel\Classes\LaravelExcelWorksheet;
use Maatwebsite\Excel\Writers\LaravelExcelWriter;

class BudgetCostByDisciplineReport
{
    /**
     * @var Project
     */
    private $project;

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
        $costs = $costs->map(function($cost) use ($total_cost) {
            $cost->weight = $total_cost? $cost->budget_cost * 100 / $total_cost : 0;
            return $cost;
        });

        return ['project' => $this->project, 'costs' => $costs, 'total_cost' => $total_cost];
    }

    function excel()
    {
        \Excel::create(slug($this->project->name) . '-budget_cost_by_discipline', function (LaravelExcelWriter $excel){
            $excel->sheet('Budget Cost by Discipline', function (LaravelExcelWorksheet $sheet) {
                $sheet->row(1, ['Discipline', 'Budget Cost', 'Weight']);
            });

            $excel->download('xlsx');
        });
    }
}