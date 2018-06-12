<?php
/**
 * Created by PhpStorm.
 * User: hazem
 * Date: 8/17/17
 * Time: 3:56 PM
 */

namespace App\Reports\Budget;


use App\Boq;
use App\BreakDownResourceShadow;
use App\Project;
use App\WbsLevel;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Classes\LaravelExcelWorksheet;
use Maatwebsite\Excel\Writers\CellWriter;
use Maatwebsite\Excel\Writers\LaravelExcelWriter;
use Symfony\Component\Debug\Exception\FatalThrowableError;

class BudgetCostDryCostByBuildingReport
{
    /** @var Project */
    protected $project;

    /** @var Collection */
    protected $tree;

    /** @var Collection */
    protected $wbs_levels;

    /** @var Collection */
    protected $info;

    /** @var Collection */
    protected $budget_costs;

    /** @var Collection */
    protected $boqs;

    /** @var int */
    protected $row = 1;

    public function __construct(Project $project)
    {
        $this->project = $project;
    }

    function run()
    {
        $this->wbs_levels = $this->project->wbs_levels->sortBy('name')->groupBy('parent_id');

        $this->budget_costs = BreakDownResourceShadow::where('project_id', $this->project->id)
            ->budgetOnly()->selectRaw('boq_wbs_id as wbs_id, sum(budget_cost) as cost')
            ->groupBy('boq_wbs_id')->get()->keyBy('wbs_id');

        $this->boqs = Boq::whereProjectId($this->project->id)
            ->selectRaw('wbs_id, sum(boqs.quantity * boqs.dry_ur) as dry_cost')
            ->groupBy('wbs_id')->get()->keyBy('wbs_id');

        $this->tree = $this->buildTree();

        return ['project' => $this->project, 'tree' => $this->tree];
    }

    function buildTree($parent_id = 0)
    {
        $tree = $this->wbs_levels->get($parent_id) ?: collect();

        return $tree->map(function (WbsLevel $level) {
            $level->subtree = $this->buildTree($level->id);

            $dry_cost = $this->boqs->get($level->id)->dry_cost ?? 0;
            $budget_cost = $this->budget_costs->get($level->id)->cost ?? 0;

            $level->dry_cost = $dry_cost + $level->subtree->sum('dry_cost');
            $level->cost = $budget_cost + $level->subtree->sum('cost');

            $level->difference = $level->cost - $level->dry_cost;
            $level->increase = $level->dry_cost? $level->difference * 100 / $level->dry_cost : 0;

            return $level;
        })->filter(function ($level) {
            return $this->boqs->has($level->id) || $level->subtree->count();
        });
    }

    function excel()
    {
        \Excel::create(slug($this->project->name) . '-budget_cost_vs_dry_by_building', function(LaravelExcelWriter $writer) {
            $writer->sheet('BudgetCostVSDryCostBuilding', function (LaravelExcelWorksheet $sheet) {
                $this->sheet($sheet);
            });

            $writer->download('xlsx');
        });
    }

    function sheet(LaravelExcelWorksheet $sheet)
    {
        $this->run();

        $sheet->row(1, ['WBS Level', 'Budget Cost', 'Dry Cost', 'Difference', 'Increase']);

        $sheet->cells("A1:E1", function(CellWriter $cells) {
            $cells->setFont(['bold' => true])->setBackground('#5182bb')->setFontColor('#ffffff');
        });

        $this->tree->each(function($level) use ($sheet) {
            $this->buildExcelTree($sheet, $level);
        });

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

        $sheet->freezeFirstRow();
        $sheet->setAutoFilter();

        return $sheet;
    }

    protected function buildExcelTree(LaravelExcelWorksheet $sheet, $level, $depth = 0)
    {
        ++$this->row;

        $sheet->row($this->row, [
            $level->name, $level->cost, $level->dry_cost, $level->difference, $level->increase / 100
        ]);

        if ($depth) {
            $sheet->cells("A{$this->row}", function(CellWriter $cells) use ($depth) {
                $cells->setTextIndent($depth * 4);
            });

            $sheet->getRowDimension($this->row)
                ->setOutlineLevel($depth < 8 ? $depth : 8)
                ->setVisible(false)->setCollapsed(true);
        }

        if ($level->subtree->count()) {
            $sheet->cells("A{$this->row}:E{$this->row}", function(CellWriter $cells) use ($depth) {
                $cells->setFont(['bold' => true]);
            });

            $level->subtree->each(function($sub_level) use ($sheet, $depth) {
                $this->buildExcelTree($sheet, $sub_level, $depth + 1);
            });
        }
    }

}