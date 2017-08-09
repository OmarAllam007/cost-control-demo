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


        $this->resources = Resources::orderBy('name')->with('types')
            ->find($this->resources_info->keys()->toArray())
            ->groupBy('resource_type_id');

        $this->divisions = ResourceType::orderBy('name')
            ->get()->groupBy('parent_id');

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
                $resource->budget_unit = $info->budget_unit ?: 0;
                return $resource;
            });

            $division->budget_cost = $division->subtree->reduce(function ($sum, $division) {
                return $sum + $division->budget_cost;
            }, $division->resources->reduce(function ($sum, $resource) {
                return $sum + $resource->budget_cost;
            }, 0));

            return $division;
        });

        return $tree;
    }

    function excel()
    {
        \Excel::create(slug($this->project->name) . '_std_activity.xlsx', function(LaravelExcelWriter $writer) {

            $writer->sheet('Std Activity', function (LaravelExcelWorksheet $sheet) {
                $sheet->row(1, ['Activity', 'Budget Cost']);
                $sheet->cells('A1:B1', function(CellWriter $cells) {
                    $cells->setFont(['bold' => true])->setBackground('#3f6caf')->setFontColor('#ffffff');
                });

                $this->tree->each(function ($division) use ($sheet) {
                    $this->buildExcel($sheet, $division);
                });

                $sheet->setColumnFormat(["B2:B{$this->row}" => '#,##0.00']);

                $sheet->setAutoFilter();
                $sheet->freezeFirstRow();
            });

            $writer->download('xlsx');
        });
    }

    protected function buildExcel(LaravelExcelWorksheet $sheet, $division, $depth = 0)
    {
        $hasChildren = $division->subtree->count() || $division->std_activities->count();
        if (!$hasChildren) {
            return;
        }

        $this->row++;
        $name = (str_repeat(' ', $depth * 6)) . $division->code . ' ' . $division->name;
        $sheet->row($this->row, [$name, $division->cost]);
        $sheet->cells("A{$this->row}:B{$this->row}", function (CellWriter $cells) {
            $cells->setFont(['bold' => true]);
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

        $division->std_activities->each(function ($activity) use ($sheet, $depth) {
            $name = (str_repeat(' ', $depth * 6)) . $activity->name;
            $sheet->row(++$this->row, [$name, $activity->cost]);
            $sheet->getRowDimension($this->row)
                ->setOutlineLevel($depth < 7 ? $depth : 7)
                ->setVisible(false)->setCollapsed(true);
        });

    }
}