<?php
/**
 * Created by PhpStorm.
 * User: hazem
 * Date: 8/14/17
 * Time: 8:34 AM
 */

namespace App\Reports\Budget;


use App\BreakDownResourceShadow;
use App\Project;
use App\ResourceType;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Classes\LaravelExcelWorksheet;
use Maatwebsite\Excel\Writers\CellWriter;
use Maatwebsite\Excel\Writers\LaravelExcelWriter;

class ManPowerReport
{
    /** @var Project */
    protected $project;

    /** @var Collection */
    protected $resources;

    /** @var int */
    protected $row = 1;

    /** @var Collection */
    protected $tree;

    /** @var Collection */
    protected $resourceTypes;

    public function __construct(Project $project)
    {
        $this->project = $project;
    }

    public function run()
    {
        $this->resources = BreakDownResourceShadow::from('break_down_resource_shadows as sh')
            ->where('sh.project_id', $this->project->id)
            ->where('sh.resource_type_id', 2)
            ->selectRaw(
                'r.resource_type_id, sh.resource_id, sh.resource_code, sh.resource_name, ' .
                'sh.measure_unit, sum(sh.budget_unit) budget_unit, sum(sh.budget_cost) budget_cost'
            )->join('resources as r', 'sh.resource_id', '=', 'r.id')
            ->groupBy(['r.resource_type_id', 'sh.resource_id', 'sh.resource_code', 'sh.resource_name', 'sh.measure_unit'])
            ->orderBy('resource_name')
            ->get()->groupBy('resource_type_id');

        $this->resourceTypes = ResourceType::whereParentId(2)->get()->groupBy('parent_id');

        $this->tree = $this->buildTree();

        return ['project' => $this->project, 'tree' => $this->tree];
    }

    public function excel()
    {
        $this->run();

        \Excel::create(slug($this->project->name) . '_man-power', function (LaravelExcelWriter $writer) {
            $writer->sheet('Man Power', function (LaravelExcelWorksheet $sheet) {
                $this->sheet($sheet);
            });

            $writer->download('xlsx');
        });

    }

    protected function buildTree($parent = 2)
    {
        $tree = $this->resourceTypes->get($parent) ?? collect();

        $tree = $tree->filter(function ($subtype) {
            return $this->resources->has($subtype->id);
        })->map(function (ResourceType $type) {
            $type->subtypes = $this->buildTree($type->id);
            $type->labours = $this->resources->get($type->id);

            $type->budget_cost = $type->labours->sum('budget_cost') + $type->subtypes->sum('budget_cost');


            return $type;
        })->reject(function (ResourceType $type) {
            return $type->subtypes->isEmpty() && $type->labours->isEmpty();
        });

        return $tree;
    }

    public function sheet(LaravelExcelWorksheet $sheet)
    {
        $this->run();

        $sheet->row($this->row, ['Description', 'Code', 'Unit of Measure', 'Budget Unit', 'Budget Cost']);
        $sheet->cells("A{$this->row}:E{$this->row}", function ($cells) {
            $cells->setFont(['bold' => true])->setBackground('#3f6caf')->setFontColor('#ffffff');
        });

        $this->tree->each(function ($type) use ($sheet) {
            $this->buildSheet($sheet, $type);
        });

        $sheet->getColumnDimension('A')->setWidth(70);
        $sheet->setAutoFilter();
        $sheet->setAutoSize(['B', 'C', 'D', 'E']);
        $sheet->setAutoSize(false);
    }

    protected function buildSheet(LaravelExcelWorksheet $sheet, $type, $depth = 0)
    {
        ++$this->row;
        $sheet->mergeCells("A{$this->row}:D{$this->row}");
        $sheet->setCellValue("A{$this->row}", $type->name);
        $sheet->setCellValue("E{$this->row}", $type->budget_cost);

        if ($depth) {
            $sheet->getRowDimension($this->row)->setVisible(false)->setCollapsed(true)->setOutlineLevel($depth > 7 ? 7 : $depth);
            $sheet->cells("A{$this->row}", function(CellWriter $cells) use ($depth) {
                $cells->setTextIndent(5 * $depth);
            });
        }

        $type->subtypes->each(function ($type) use ($sheet, $depth) {
            $this->buildSheet($sheet, $type, $depth + 1);
        });

        ++$depth;
        $type->labours->each(function ($resource) use ($sheet, $depth) {
            $sheet->row(++$this->row, [
                $resource->resource_name, $resource->resource_code, $resource->measure_unit, $resource->budget_unit, $resource->budget_cost
            ]);

            $sheet->getRowDimension($this->row)->setVisible(false)->setCollapsed(true)->setOutlineLevel($depth > 7 ? 7 : $depth);
            $sheet->cells("A{$this->row}", function(CellWriter $cells) use ($depth) {
                $cells->setTextIndent(5 * $depth);
            });
        });

    }
}