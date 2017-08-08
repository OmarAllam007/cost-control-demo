<?php

namespace App\Reports\Budget;


use App\ActivityDivision;
use App\BreakDownResourceShadow;
use App\CsiCategory;
use App\Productivity;
use App\Project;
use App\StdActivity;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Classes\LaravelExcelWorksheet;
use Maatwebsite\Excel\Writers\CellWriter;
use Maatwebsite\Excel\Writers\LaravelExcelWriter;

class ProductivityReport
{
    /** @var Collection */
    protected $productivity_info;

    /** @var Collection */
    protected $productivities;

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
        $this->productivity_info = BreakDownResourceShadow::query()
            ->whereProjectId($this->project->id)
            ->whereNotNull('productivity_id')
            ->selectRaw('DISTINCT productivity_id')
            ->pluck('productivity_id');

        $this->productivities = Productivity::orderBy('description')
            ->with('units')
            ->find($this->productivity_info->toArray())
            ->groupBy('csi_category_id');

        $this->divisions = CsiCategory::orderBy('code')->orderBy('name')
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

        $tree->map(function (CsiCategory $division) {
            $division->subtree = $this->buildTree($division->id)->filter(function ($division) {
                return $division->subtree->count() || $division->productivities->count();
            });

            $division->productivities = $this->productivities->get($division->id) ?: collect();

            return $division;
        })->filter(function ($division) {
            return $division->subtree->count() || $division->productivities->count();
        });

        return $tree;
    }

    function excel()
    {
        \Excel::create(slug($this->project->name) . '_std_activity.xlsx', function(LaravelExcelWriter $writer) {

            $writer->sheet('Productivity', function (LaravelExcelWorksheet $sheet) {
                $sheet->row(1, ['Productivity', 'Budget Cost']);
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