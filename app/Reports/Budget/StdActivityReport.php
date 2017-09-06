<?php

namespace App\Reports\Budget;


use App\ActivityDivision;
use App\BreakDownResourceShadow;
use App\Project;
use App\StdActivity;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Classes\LaravelExcelWorksheet;
use Maatwebsite\Excel\Writers\CellWriter;
use Maatwebsite\Excel\Writers\LaravelExcelWriter;

class StdActivityReport
{
    /** @var Collection */
    protected $activity_info;

    /** @var Collection */
    protected $activities;

    /** @var Project */
    protected $project;

    /** @var Collection */
    protected $tree;

    /** @var Collection */
    protected $divisions;

    protected $row = 1;

    /** @var bool */
    private $includeCost;

    public function __construct(Project $project, $includeCost = true)
    {
        $this->project = $project;
        $this->includeCost = $includeCost;
    }

    function run()
    {
        $this->activity_info = BreakDownResourceShadow::whereProjectId($this->project->id)
            ->selectRaw('activity_id, sum(budget_cost) as budget_cost')
            ->groupBy('activity_id')
            ->orderBy('activity')
            ->pluck('budget_cost', 'activity_id');


        $this->activities = StdActivity::orderBy('name')
            ->find($this->activity_info->keys()->toArray())
            ->groupBy('division_id');

        $this->divisions = ActivityDivision::orderBy('code')->orderBy('name')
            ->get()->groupBy('parent_id');

        $this->tree = $this->buildTree();

        return ['project' => $this->project, 'tree' => $this->tree, 'includeCost' => $this->includeCost];
    }

    /**
     * @param int $parent
     * @return Collection
     */
    protected function buildTree($parent = 0)
    {
        $tree = $this->divisions->get($parent) ?: collect();

        $tree->map(function (ActivityDivision $division) {
            $division->subtree = $this->buildTree($division->id)
                ->filter(function (ActivityDivision $division) {
                    return $division->subtree->count() || $division->std_activities->count();
                });

            $division->std_activities = $this->activities->get($division->id) ?: collect();

            if ($this->includeCost) {
                $cost = $division->std_activities->map(function ($activity) {
                    $activity->cost = $this->activity_info->get($activity->id) ?: 0;
                    return $activity;
                })->reduce(function ($sum, $activity) {
                    return $sum + $activity->cost;
                }, 0);

                $division->cost = $division->subtree->reduce(function ($sum, $division) {
                    return $sum + $division->cost;
                }, $cost);
            }

            return $division;
        });

        return $tree;
    }

    function excel()
    {
        \Excel::create(slug($this->project->name) . '_std_activity.xlsx', function(LaravelExcelWriter $writer) {

            $writer->sheet('Std Activity', function (LaravelExcelWorksheet $sheet) {
                $sheet->row(1, ['Activity', $this->includeCost? 'Budget Cost' : '']);
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