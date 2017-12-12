<?php

namespace App\Reports\Budget;

use App\BreakDownResourceShadow;
use App\Project;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Classes\LaravelExcelWorksheet;
use Maatwebsite\Excel\Writers\LaravelExcelWriter;

class HighPriorityMaterialsReport
{
    /** @var Project */
    protected $project;

    /** @var Collection */
    protected $tree;

    /** @var int */
    protected $row = 1;

    /** @var float */
    protected $total;

    public function __construct(Project $project)
    {
        $this->project = $project;
    }

    public function run()
    {
        $resources = $this->project->resources()->whereRaw("coalesce(top_material) != ''")->orderBy('name')->get();

        $shadows = BreakDownResourceShadow::whereIn('resource_id', $resources->pluck('id')->unique())
            ->selectRaw('resource_id, sum(budget_unit) as budget_unit, sum(budget_cost) as budget_cost')
            ->groupBy('resource_id')
            ->get()->keyBy('resource_id');

        $this->total = BreakDownResourceShadow::whereProjectId($this->project->id)->sum('budget_cost');

        $this->tree = $resources->map(function ($resource) use ($shadows) {
            $resource->budget_unit = $shadows->get($resource->id)->budget_unit ?? 0;
            $resource->budget_cost = $shadows->get($resource->id)->budget_cost ?? 0;
            return $resource;
        })->groupBy(function ($resource) {
            return strtolower($resource->top_material);
        })->map(function (Collection $group, $name) {
            $group = $group->map(function ($resource) {
                $resource->weight = $resource->budget_cost * 100 / $this->total;
                return $resource;
            });

            $total = $group->sum('budget_cost');
            $weight = $total * 100 / $this->total;

            return ['name' => strtoupper($name), 'resources' => $group, 'budget_cost' => $total, 'weight' => $weight];
        })->sortBy('name');

        return ['project' => $this->project, 'tree' => $this->tree, 'total' => $this->total];
    }

    public function excel()
    {
        \Excel::create(slug($this->project->name . '-high_priority'), function (LaravelExcelWriter $excel) {
            $excel->sheet('High Priority Materials', function (LaravelExcelWorksheet $sheet) {
                $this->sheet($sheet);
            });

            $excel->download('xlsx');
        });
    }

    public function sheet(LaravelExcelWorksheet $sheet)
    {
        $this->run();

        $sheet->row($this->row, ['Resource Name', 'Resource Code', 'Budget Unit', 'Budget Cost', 'Weight']);

        $sheet->cells("A{$this->row}:E{$this->row}", function($cells) {
            $cells->setFont(['bold' => true])->setBackground('#3f6caf')->setFontColor('#ffffff');
        });

        $this->tree->each(function ($group) use ($sheet){
            ++$this->row;
            $sheet->mergeCells("A{$this->row}:C{$this->row}");
            $sheet->setCellValue("A{$this->row}", $group['name']);
            $sheet->setCellValue("D{$this->row}", $group['budget_cost']);
            $sheet->setCellValue("E{$this->row}", $group['weight'] / 100);
            $sheet->cells("A{$this->row}:E{$this->row}", function($cells) {
                $cells->setFont(['bold' => true])->setBackground('#f5964f')->setFontColor('#ffffff');
            });

            $group['resources']->each(function ($resource) use ($sheet) {
                $sheet->row(++$this->row, [$resource->name, $resource->code, $resource->budget_unit, $resource->budget_cost, $resource->weight / 100]);

            });
        });

        $sheet->setColumnFormat(["C2:D{$this->row}" => '#,##0.00']);
        $sheet->setColumnFormat(["E2:E{$this->row}" => '0.00%']);

        $sheet->getColumnDimension('A')->setWidth(80);
        $sheet->setAutoFilter();
        $sheet->setAutoSize(['B', 'C', 'D']);
        $sheet->setAutoSize(false);
    }
}