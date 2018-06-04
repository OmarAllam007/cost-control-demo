<?php
/**
 * Created by PhpStorm.
 * User: omar.garana
 * Date: 10/9/2016
 * Time: 9:00 PM
 */

namespace App\Reports\Budget;


use App\Boq;
use App\Breakdown;
use App\BreakDownResourceShadow;
use App\Project;
use App\StdActivity;
use App\Survey;
use App\WbsLevel;
use Beta\B;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Classes\LaravelExcelWorksheet;
use Maatwebsite\Excel\Writers\CellWriter;
use Maatwebsite\Excel\Writers\LaravelExcelWriter;

class BudgetCostDryCostByDisciplineReport
{
    /** @var Project */
    protected $project;

    /** @var Collection */
    protected $disciplines;

    /** @var int */
    protected $row = 1;

    function __construct(Project $project)
    {
        $this->project = $project;
    }

    public function run()
    {
        $project = $this->project;

        /** @var Collection $budgetData */
        $budgetData = BreakDownResourceShadow::from('break_down_resource_shadows as sh')
            ->where('sh.project_id', $project->id)->budgetOnly()
            ->join('std_activities as a', 'sh.activity_id', '=', 'a.id')
            ->selectRaw("CASE WHEN a.discipline != '' THEN a.discipline ELSE 'General' END as type")
            ->selectRaw('sum(budget_cost) as budget_cost')
            ->groupBy(\DB::raw(1))->orderByRaw('1')
            ->get()->keyBy(function($row) {
                return trim(strtolower($row->type));
            });

        $boqData = Boq::whereProjectId($project->id)->groupBy('type')
            ->selectRaw('type, sum(dry_ur * quantity) as dry_cost')->get()->keyBy(function($row) {
                return trim(strtolower($row->type));
            });

        $this->disciplines = $budgetData->map(function ($cost, $type) use ($boqData) {
            $cost->dry_cost = $boqData[$type]->dry_cost ?? 0;
            $cost->difference = $cost->budget_cost - $cost->dry_cost;
            $cost->increase = $cost->dry_cost ? ($cost->difference * 100 / $cost->dry_cost) : 0;

            return $cost;
        });

//        dd($this->disciplines);

        return ['project' => $this->project, 'disciplines' => $this->disciplines];
    }

    public function excel()
    {
        \Excel::create(slug($this->project->name) . '-budget_vs_dry_discipline', function(LaravelExcelWriter $excel) {
            $excel->sheet('BudgetVsDry by Discipline', function (LaravelExcelWorksheet $sheet) {
                $this->sheet($sheet);
            });

            $excel->download('xlsx');
        });
    }

    public function sheet(LaravelExcelWorksheet $sheet)
    {
        $this->run();

        $sheet->row($this->row, ['Discipline', 'Dry Cost', 'Budget Cost', 'Difference', 'Increase']);

        $sheet->cells("A1:E1", function(CellWriter $cells) {
            $cells->setFont(['bold' => true])->setBackground('#5182bb')->setFontColor('#ffffff');
        });

        $this->disciplines->each(function($cost) use ($sheet) {
            $sheet->row(++$this->row, [
                $cost->type, $cost->dry_cost, $cost->budget_cost, $cost->difference, $cost->increase / 100
            ]);
        });

        $total_budget_cost = $this->disciplines->sum('budget_cost');
        $total_dry = $this->disciplines->sum('dry_cost');
        $total_diff = $total_budget_cost - $total_dry;
        $total_increase = $total_dry ? $total_diff / $total_dry : 0;
        $sheet->row(++$this->row, [
            'Total', $total_dry, $total_budget_cost, $total_diff, $total_increase
        ])->cells("A{$this->row}:E{$this->row}", function(CellWriter $cells) {
            $cells->setFont(['bold' => true])->setBackground('#5182bb')->setFontColor('#ffffff');
        });

        $sheet->setAutoSize(true);
        $sheet->setColumnFormat([
            "B2:B{$this->row}" => '#,##0.00',
            "C2:C{$this->row}" => '#,##0.00',
            "D2:D{$this->row}" => '#,##0.00_-',
            "E2:E{$this->row}" => '0.00%',
        ]);

        $varCondition = new \PHPExcel_Style_Conditional();
        $varCondition->setConditionType(\PHPExcel_Style_Conditional::CONDITION_CELLIS)
            ->setOperatorType(\PHPExcel_Style_Conditional::OPERATOR_LESSTHAN)->addCondition(0)
            ->getStyle()->getFont()->getColor()->setARGB(\PHPExcel_Style_Color::COLOR_RED);
        $sheet->getStyle("D2:E{$this->row}")->setConditionalStyles([$varCondition]);

        return $sheet;
    }
}