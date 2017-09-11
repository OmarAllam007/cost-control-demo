<?php

namespace App\Reports\Budget;

use App\Project;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Classes\LaravelExcelWorksheet;
use Maatwebsite\Excel\Writers\CellWriter;
use Maatwebsite\Excel\Writers\LaravelExcelWriter;

class RevisedBoqReport
{
    /** @var Project */
    protected $project;

    /** @var Collection */
    protected $boqs;

    /** @var Collection */
    protected $tree;

    /** @var Collection */
    protected $wbs_levels;

    /** @var int */
    protected $row = 1;

    function __construct(Project $project)
    {
        $this->project = $project;
    }

    function run()
    {
        $this->boqs = collect(\DB::table('break_down_resource_shadows as sh')
            ->selectRaw('distinct boq_wbs_id as wbs_id, sh.activity, boqs.description, sh.cost_account, boqs.price_ur * boqs.quantity as original_boq, boqs.price_ur * sh.eng_qty as revised_boq')
            ->join('boqs', 'sh.boq_id', '=','boqs.id')
            ->where('sh.project_id', $this->project->id)->get())
            ->groupBy('wbs_id')->map(function(Collection $group) {
                return $group->sortBy('activity')->groupBy('activity');
            });

        $this->wbs_levels = $this->project->wbs_levels->groupBy('parent_id');

        $this->tree = $this->buildTree();

        return ['tree' => $this->tree, 'project' => $this->project];
    }

    private function buildTree($parent = 0)
    {
        return $this->wbs_levels->get($parent, collect())->map(function($level) {
            $level->subtree = $this->buildTree($level->id);
            $level->activity = $this->boqs->get($level->id, collect());
            $level->original_boq = $level->activity->flatten()->sum('original_boq') + $level->subtree->sum('original_boq');
            $level->revised_boq = $level->activity->flatten()->sum('revised_boq') + $level->subtree->sum('revised_boq');

            return $level;
        })->reject(function ($level) {
            return $level->subtree->isEmpty() && $level->activity->isEmpty();
        });
    }

    function excel()
    {
        return \Excel::create(slug($this->project->name) . '-revised_boq', function(LaravelExcelWriter $excel) {
            $excel->sheet('Revised BOQ', function (LaravelExcelWorksheet $sheet) {
                $this->sheet($sheet);
            });

            $excel->download('xlsx');
        });
    }

    function sheet(LaravelExcelWorksheet $sheet)
    {
        $this->run();

        $sheet->row($this->row, ['Description', 'Cost Account', 'Original BOQ', 'Revised BOQ']);

        $sheet->cells('A1:D1', function (CellWriter $cells) {
            $cells->setFont(['bold' => true])->setBackground('#3f6caf')->setFontColor('#ffffff');
        });

        $this->tree->each(function($level) use ($sheet) {
            $this->buildSheet($sheet, $level);
        });

//        $sheet->setAutoFilter();
        $sheet->freezeFirstRow();
        $sheet->setColumnFormat([
            "C2:C{$this->row}" => '#,##0.00',
            "D2:D{$this->row}" => '#,##0.00',
        ]);

        $sheet->getColumnDimension('A')->setWidth(80);
        $sheet->setAutoSize(['B', "C", "D"]);
        $sheet->setAutoSize(false);
    }

    protected function buildSheet(LaravelExcelWorksheet $sheet, $level, $depth = 0)
    {
        $sheet->row(++$this->row, [$level->name, '', $level->original_boq, $level->revised_boq]);

        if ($depth) {
            $sheet->getRowDimension($this->row)
                ->setVisible(false)->setCollapsed(true)
                ->setOutlineLevel(min($depth, 7));

            $sheet->cells("A{$this->row}", function(CellWriter $cells) use ($depth) {
                $cells->setTextIndent(6 * $depth);
            });
        }

        ++$depth;
        $level->subtree->each(function($sublevel) use ($sheet, $depth) {
            $this->buildSheet($sheet, $sublevel, $depth);
        });

        $level->activity->each(function(Collection $items, $name) use ($sheet, $depth) {
            $sheet->row(++$this->row, [$name, '', $items->sum('original_boq'), $items->sum('revised_boq')]);
            $sheet->getRowDimension($this->row)
                ->setVisible(false)->setCollapsed(true)
                ->setOutlineLevel(min($depth, 7));

            $sheet->cells("A{$this->row}", function(CellWriter $cells) use ($depth) {
                $cells->setTextIndent(6 * $depth);
            });

            $items->each(function($item) use ($sheet, $depth) {
                $sheet->row(++$this->row, [$item->description, $item->cost_account, $item->original_boq, $item->revised_boq]);
                $sheet->getRowDimension($this->row)
                    ->setVisible(false)->setCollapsed(true)
                    ->setOutlineLevel(min($depth + 1, 7));

                $sheet->cells("A{$this->row}", function(CellWriter $cells) use ($depth) {
                    $cells->setTextIndent(6 * ($depth + 1));
                });
            });
        });
    }
}