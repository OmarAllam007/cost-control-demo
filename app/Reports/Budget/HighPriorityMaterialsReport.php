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

    public function __construct(Project $project)
    {
        $this->project = $project;
    }

    public function run()
    {
        $resources = $this->project->resources()->whereNotNull('top_material')->orderBy('name')->get();

        $shadows = BreakDownResourceShadow::whereIn('resource_id', $resources->pluck('id')->unique())
            ->selectRaw('resource_id, sum(budget_unit) as budget_unit, sum(budget_cost) as budget_cost')
            ->groupBy('resource_id')
            ->get()->keyBy('resource_id');

        $this->tree = $resources->map(function ($resource) use ($shadows) {
            $resource->budget_unit = $shadows->get($resource->id)->budget_unit ?? 0;
            $resource->budget_cost = $shadows->get($resource->id)->budget_cost ?? 0;
            return $resource;
        })->groupBy(function ($resource) {
            return strtolower(trim($resource->top_material));
        })->map(function (Collection $group, $name) {
            return ['name' => strtoupper($name), 'resources' => $group, 'budget_cost' => $group->sum('budget_cost')];
        })->sortBy('name');

        return ['project' => $this->project, 'tree' => $this->tree];
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

        $sheet->row($this->row, ['Resource Name', 'Resource Code', 'Budget Unit', 'Budget Cost']);

        $sheet->cells("A{$this->row}:D{$this->row}", function($cells) {
            $cells->setFont(['bold' => true])->setBackground('#3f6caf')->setFontColor('#ffffff');
        });

        $this->tree->each(function ($group) use ($sheet){
            ++$this->row;
            $sheet->mergeCells("A{$this->row}:C{$this->row}");
            $sheet->setCellValue("A{$this->row}", $group['name']);
            $sheet->setCellValue("D{$this->row}", $group['budget_cost']);
            $sheet->cells("A{$this->row}:D{$this->row}", function($cells) {
                $cells->setFont(['bold' => true])->setBackground('#f5964f')->setFontColor('#ffffff');
            });

            $group['resources']->each(function ($resource) use ($sheet) {
                $sheet->row(++$this->row, [$resource->name, $resource->code, $resource->budget_unit, $resource->budget_cost]);

            });
        });

        $sheet->setColumnFormat(["C2:D{$this->row}" => '#,##0.00']);

        $sheet->getColumnDimension('A')->setWidth(80);
        $sheet->setAutoFilter();
        $sheet->setAutoSize(['B', 'C', 'D']);
        $sheet->setAutoSize(false);
    }
}