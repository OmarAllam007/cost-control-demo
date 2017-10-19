<?php

namespace App\Reports\Budget;


use App\ActivityDivision;
use App\BreakDownResourceShadow;
use App\Project;
use App\Resources;
use App\ResourceType;
use App\StdActivity;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Classes\LaravelExcelWorksheet;
use Maatwebsite\Excel\Writers\CellWriter;
use Maatwebsite\Excel\Writers\LaravelExcelWriter;

class ResourceDictReport
{
    /** @var Collection */
    protected $resources_info;

    /** @var Collection */
    protected $resources;

    /** @var Project */
    protected $project;

    /** @var Collection */
    protected $tree;

    /** @var Collection */
    protected $divisions;

    /** @var float */
    protected $total;

    protected $row = 1;

    public function __construct(Project $project)
    {
        $this->project = $project;
    }

    function run()
    {
        $this->resources_info = BreakDownResourceShadow::whereProjectId($this->project->id)
            ->selectRaw('resource_id, sum(budget_unit) as budget_unit, sum(budget_cost) as budget_cost')
            ->groupBy('resource_id')
            ->orderBy('resource_code')->orderBy('resource_name')
            ->get()->keyBy('resource_id');


        $this->resources = Resources::orderBy('name')->with(['types', 'parteners'])
            ->find($this->resources_info->keys()->toArray())
            ->groupBy('resource_type_id');

        $this->divisions = ResourceType::orderBy('name')
            ->get()->groupBy('parent_id');

        $this->total = BreakDownResourceShadow::whereProjectId($this->project->id)->sum('budget_cost');

        $this->tree = $this->buildTree();

        return ['project' => $this->project, 'tree' => $this->tree];
    }

    /**
     * @param int $parent
     * @return Collection
     */
    protected function buildTree($parent = 0)
    {
        $tree = $this->divisions->get($parent) ?: collect();

        $tree->map(function (ResourceType $division) {
            $division->subtree = $this->buildTree($division->id)
                ->filter(function (ResourceType $division) {
                    return $division->subtree->count() || $division->resources->count();
                });

            $division->resources = $this->resources->get($division->id) ?: collect();

            $division->resources->map(function ($resource) {
                $info = $this->resources_info->get($resource->id) ?: collect();
                $resource->budget_cost = $info->budget_cost ?: 0;
                $resource->weight = $resource->budget_cost * 100 / $this->total;
                $resource->budget_unit = $info->budget_unit ?: 0;
                return $resource;
            });

            $division->budget_cost = $division->subtree->sum('budget_cost') +
                $division->resources->sum('budget_cost');

            $division->weight = $division->budget_cost * 100 / $this->total;

            return $division;
        });

        return $tree;
    }

    function excel()
    {
        \Excel::create(slug($this->project->name) . '_std_activity.xlsx', function(LaravelExcelWriter $writer) {
            $writer->sheet('Resource Dictionary', function (LaravelExcelWorksheet $sheet) {
                $this->sheet($sheet);
            });

            $writer->download('xlsx');
        });
    }

    public function sheet($sheet)
    {
        $this->run();

        $sheet->row(1, ['Resource', 'Code', 'Rate', 'Unit of measure', 'Supplier/Subcontractor', 'Reference','Waste (%)', 'Budget Unit', 'Budget Cost', 'Weight']);
        $sheet->cells('A1:I1', function(CellWriter $cells) {
            $cells->setFont(['bold' => true])->setBackground('#3f6caf')->setFontColor('#ffffff');
        });

        $this->tree->each(function ($division) use ($sheet) {
            $this->buildExcel($sheet, $division);
        });

        $sheet->setColumnFormat([
            "B2:B{$this->row}" => '@',
            "C2:C{$this->row}" => '#,##0.00',
            "G2:G{$this->row}" => '#,##0.00',
            "H2:H{$this->row}" => '#,##0.00',
            "I2:I{$this->row}" => '#,##0.00',
            "J2:J{$this->row}" => '0.00%',
        ]);

//        $sheet->setAutoFilter();
        $sheet->freezeFirstRow();
        $sheet->getColumnDimension('A')->setAutoSize(false)->setWidth(80);
        $sheet->getColumnDimension('E')->setAutoSize(false)->setWidth(20);
        $sheet->getColumnDimension('F')->setAutoSize(false)->setWidth(20);
        $sheet->setAutoSize(['B', 'C', 'D', 'G', 'H', 'I', 'J']);
        $sheet->setAutoSize(false);
    }

    protected function buildExcel(LaravelExcelWorksheet $sheet, $division, $depth = 0)
    {
        $hasChildren = $division->subtree->count() || $division->resources->count();
        if (!$hasChildren) {
            return;
        }

        $this->row++;
//        $name = (str_repeat(' ', $depth * 6)) . $division->name;
        $name = $division->name;
        $sheet->mergeCells("A{$this->row}:H{$this->row}");
        $sheet->setCellValue("A{$this->row}", $name);
        $sheet->setCellValue("I{$this->row}", $division->budget_cost ?: 0);
        $sheet->setCellValue("J{$this->row}", $division->weight / 100);
        $sheet->cells("A{$this->row}:J{$this->row}", function (CellWriter $cells) {
            $cells->setFont(['bold' => true]);
        });

        $sheet->cells("A{$this->row}", function ($cells) use ($depth) {
            $cells->setTextIndent($depth * 4);
        });

        if ($depth) {
            $sheet->getRowDimension($this->row)
                ->setOutlineLevel($depth < 7 ? $depth : 7)
                ->setVisible(false)->setCollapsed(true);
        }

        ++$depth;

        $division->subtree->each(function($subdivision) use ($sheet, $depth) {
            $this->buildExcel($sheet, $subdivision, $depth);
        });

        $division->resources->each(function ($resource) use ($sheet, $depth) {
//            $name = (str_repeat(' ', $depth * 6)) . $resource->name;
            $name = $resource->name;
            $sheet->row(++$this->row, [
                $name, $resource->resource_code, $resource->rate, $resource->units->type ?? '',
                $resource->parteners->name ?? '', $resource->reference,
                $resource->waste, $resource->budget_unit, $resource->budget_cost, $resource->weight / 100
            ]);
            $sheet->getRowDimension($this->row)
                ->setOutlineLevel($depth < 7 ? $depth : 7)
                ->setVisible(false)->setCollapsed(true);

            $sheet->cells("A{$this->row}", function ($cells) use ($depth) {
                $cells->setTextIndent($depth * 4);
            });
        });

    }
}