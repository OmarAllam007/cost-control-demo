<?php

namespace App\Reports\Budget;


use App\BreakDownResourceShadow;
use App\Project;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Classes\LaravelExcelWorksheet;
use Maatwebsite\Excel\Writers\CellWriter;

class WbsDictionary
{
    /** @var Project */
    protected $project;

    /** @var Collection */
    protected $tree;

    /** @var Collection */
    protected $wbs_levels;

    /** @var int */
    protected $row = 1;

    /** @var float */
    protected $total;

    /** @var Collection */
    protected $resources;

    public function __construct(Project $project)
    {
        $this->project = $project;
    }

    public function run()
    {
        $this->wbs_levels = $this->project->wbs_levels->groupBy('parent_id');

        $this->total = BreakDownResourceShadow::where('project_id', $this->project->id)->budgetOnly()->sum('budget_cost');

        $this->resources = BreakDownResourceShadow::where('project_id', $this->project->id)->budgetOnly()
            ->selectRaw('wbs_id, resource_name, resource_code, sum(budget_unit) as budget_unit, avg(unit_price) as unit_price, sum(budget_cost) as cost')
            ->groupBy(['wbs_id', 'resource_name', 'resource_code'])
            ->get()->map(function($resource) {
                $resource->weight = $resource->cost * 100 / $this->total;
                return $resource;
            })->groupBy('wbs_id');

        $this->tree = $this->buildTree();

        return ['project' => $this->project, 'tree' => $this->tree, 'total' => $this->total];
    }

    public function excel()
    {
        \Excel::create(slug($this->project->name) . '_wbs-dictionary', function($excel) {
            $excel->sheet('WBS Dictionary', function ($sheet) {
                $this->sheet($sheet);
            });

            return $excel->download('xlsx');
        });
    }

    public function sheet(LaravelExcelWorksheet $sheet)
    {
        $this->run();

        $sheet->row($this->row, ['Wbs Level', 'Resource Name', 'Resource Code', 'Budget Unit', 'Price/Unit', 'Budget Cost', 'Weight']);
        $sheet->cells("A{$this->row}:G{$this->row}", function(CellWriter $cells) {
            $cells->setFont(['bold' => true])->setBackground('#3f6caf')->setFontColor('#ffffff');
        });

        $this->tree->each(function($level) use ($sheet) {
            $this->buildExcel($sheet, $level);
        });

        $sheet->setColumnFormat([
            "D2:F{$this->row}" => '#,##0.00',
            "G2:G{$this->row}" => '0.00%'
        ]);

        $sheet->setAutoSize(false);
    }

    protected function buildTree($parent = 0)
    {
        return $this->wbs_levels->get($parent, collect())->map(function ($level) {
            $level->subtree = $this->buildTree($level->id);

            $level->resource_dict = $this->resources->get($level->id, collect());

            $level->cost = $level->subtree->sum('cost') + $level->resource_dict->sum('cost');
            $level->weight = $level->subtree->sum('weight') + $level->resource_dict->sum('weight');

            return $level;
        })->reject(function ($level) {
            return $level->subtree->isEmpty() && $level->resource_dict->isEmpty();
        });
    }

    protected function buildExcel(LaravelExcelWorksheet $sheet, $level, $depth = 0)
    {
        $sheet->row(++$this->row, [$level->name . ' (' . $level->code . ')', '', '', '', '', $level->cost, $level->weight / 100]);

        $sheet->cells("A{$this->row}:G{$this->row}", function(CellWriter $cells) {
            $cells->setFont(['bold' => true]);
        });

        if ($depth) {
            $sheet->getRowDimension($this->row)->setOutlineLevel(min($depth, 8))->setCollapsed(true)->setVisible(false);

            $sheet->cells("A{$this->row}", function(CellWriter $cells) use ($depth) {
                $cells->setTextIndent(6 * $depth);
            });
        }

        ++$depth;
        $level->subtree->each(function ($sub_level) use ($sheet, $depth) {
            $this->buildExcel($sheet, $sub_level, $depth);
        });

        $level->resource_dict->each(function($resource) use ($sheet, $depth){
            $sheet->row(++$this->row, [
                '', $resource->resource_name, $resource->resource_code,
                $resource->budget_unit, $resource->unit_price, $resource->cost, $resource->weight / 100
            ]);

            $sheet->getRowDimension($this->row)->setOutlineLevel(min($depth, 8))->setCollapsed(true)->setVisible(false);
        });

    }

}