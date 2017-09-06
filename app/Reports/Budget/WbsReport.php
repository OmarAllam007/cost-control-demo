<?php

namespace App\Reports\Budget;

use App\BreakDownResourceShadow;
use App\Project;
use App\WbsLevel;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Classes\LaravelExcelWorksheet;
use Maatwebsite\Excel\Writers\CellWriter;
use Maatwebsite\Excel\Writers\LaravelExcelWriter;

/**
* Generates WBS Report
*/
class WbsReport
{
    /** @var Project */
    protected $project;

    /** @var Collection */
    protected $wbs_levels;

    /** @var Collection */
    protected $costs;

    protected $row = 2;

    /** @var bool */
    protected $includeCost;

    function __construct(Project $project, $includeCost = true)
    {
        $this->project = $project;
        $this->includeCost = $includeCost;
    }

    function run()
    {
        $this->wbs_levels = $this->project->wbs_levels->groupBy('parent_id');

        $this->costs = BreakDownResourceShadow::whereProjectId($this->project->id)
                ->selectRaw('wbs_id, sum(budget_cost) as cost')
                ->groupBy('wbs_id')->pluck('cost', 'wbs_id');

        $this->tree = $this->buildTree(0);

        return ['project' => $this->project, 'wbsTree' => $this->tree, 'includeCost' => $this->includeCost];
    }

    function buildTree($parent_id)
    {
        $tree = $this->wbs_levels->get($parent_id) ?: collect();

        return $tree->map(function(WbsLevel $wbs_level) {
            $wbs_level->subtree = $this->buildTree($wbs_level->id);

            if ($this->includeCost) {
                $cost = $wbs_level->subtree->reduce(function ($sum, WbsLevel $level) {
                    return $sum + $level->cost;
                }, $this->costs->get($wbs_level->id) ?: 0);

                $wbs_level->cost = $cost;
            }

            return $wbs_level;
        });
    }

    function excel()
    {
        $this->run();

        \Excel::create(slug($this->project->name) . '_wbs-tree', function(LaravelExcelWriter $writer) {
            $writer->sheet('WBS', function (LaravelExcelWorksheet $sheet) {
                $sheet->row(1, ['WBS Level', 'Code', $this->includeCost? 'Budget Cost' : '']);
                $this->tree->each(function(WbsLevel $level) use ($sheet) {
                    $this->buildExcel($sheet, $level);
                });

                $sheet->setAutoFilter();
                $sheet->freezeFirstRow();
                $sheet->cells('A1:C1', function(CellWriter $cells) {
                    $cells->setFont(['bold' => true])->setBackground('#3f6caf')->setFontColor('#ffffff');
                });

                $sheet->setColumnFormat(["C2:C{$this->row}" => '#,##0.00']);
            });

            $writer->download('xlsx');
        });
    }

    protected function buildExcel(LaravelExcelWorksheet $sheet, WbsLevel $level, $depth = 0)
    {
        $prefix = $depth ? str_repeat(' ', 6 * $depth + 1) : '';
        $name = $prefix . $level->name;
        $sheet->row($this->row, [$name, $level->code, $level->cost ?: '']);

        if ($depth) {
            $sheet->getRowDimension($this->row)
                ->setOutlineLevel($depth < 7 ? $depth : 7)
                ->setVisible(false)
                ->setCollapsed(true);
        } else {
            $sheet->getRowDimension($this->row)
                ->setVisible(true)
                ->setCollapsed(false);
        }

        if ($level->subtree->count()) {
            $sheet->cells("A{$this->row}:C{$this->row}", function (CellWriter $cells) {
                $cells->setFont(['bold' => true]);
            });
        }

        ++$this->row;

        $level->subtree->each(function($sub_level) use ($sheet, $depth) {
            $this->buildExcel($sheet, $sub_level, $depth + 1);
        });
    }


}