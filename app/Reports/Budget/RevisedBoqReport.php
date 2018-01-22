<?php

namespace App\Reports\Budget;

use App\BreakDownResourceShadow;
use App\Project;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Collection;
use Illuminate\Support\Fluent;
use Maatwebsite\Excel\Classes\LaravelExcelWorksheet;
use Maatwebsite\Excel\Writers\CellWriter;
use Maatwebsite\Excel\Writers\LaravelExcelWriter;

class RevisedBoqReport
{
    /** @var Project */
    protected $project;

    /** @var Collection */
    protected $revised_boqs;

    /** @var Collection */
    protected $original_boqs;

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
        $this->original_boqs = collect(\DB::table('boqs')
            ->selectRaw('wbs_id, cost_account, description, (price_ur * quantity) as original_boq')
            ->where('project_id', $this->project->id)->get())
            ->groupBy('wbs_id')->map(function (Collection $group) {
                return $group->keyBy('cost_account');
            });

        $this->revised_boqs = collect(
            \DB::table('qty_surveys as qs')
                ->where('qs.project_id', $this->project->id)
                ->join('boqs as boq', function (JoinClause $on) {
                    $on->on('qs.boq_id', '=', 'boq.id');
                    $on->on('qs.cost_account', '=', 'boq.cost_account');
                })
                ->selectRaw('boq.wbs_id as boq_wbs_id, boq.cost_account, eng_qty, boq.price_ur, count(DISTINCT qs.wbs_level_id)')
                ->groupBy('boq.wbs_id', 'boq.cost_account', 'eng_qty', 'boq.price_ur')
                ->get())
            ->groupBy('boq_wbs_id')
            ->map(function (Collection $group) {
                return $group->keyBy(function ($boq) {
                    return trim($boq->cost_account);
                });
            });

        $this->wbs_levels = $this->project->wbs_levels->groupBy('parent_id');

        $this->tree = $this->buildTree();

        return ['tree' => $this->tree, 'project' => $this->project];
    }

    private function buildTree($parent = 0)
    {
        return $this->wbs_levels->get($parent, collect())->map(function ($level) {
            $level->subtree = $this->buildTree($level->id);

            $level->cost_accounts = $this->original_boqs->get($level->id, collect())->map(function ($cost_account) {
                $revised = $cost_account->revised_boq = $this->revised_boqs
                    ->get($cost_account->wbs_id, collect())->get(trim($cost_account->cost_account));

                if ($revised) {
                    $cost_account->revised_boq = $revised->eng_qty * $revised->price_ur;
                } else {
                    $cost_account->revised_boq = 0;
                }

                return $cost_account;
            })->reject(function ($cost_account) {
                return $cost_account->revised_boq == 0 && $cost_account->original_boq == 0;
            });


            $level->original_boq = $level->cost_accounts->sum('original_boq') + $level->subtree->sum('original_boq');
            $level->revised_boq = $level->cost_accounts->sum('revised_boq') + $level->subtree->sum('revised_boq');

            return $level;
        })->reject(function ($level) {
            return $level->subtree->isEmpty() && $level->cost_accounts->isEmpty();
        });
    }

    function excel()
    {
        return \Excel::create(slug($this->project->name) . '-revised_boq', function (LaravelExcelWriter $excel) {
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

        $this->tree->each(function ($level) use ($sheet) {
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

            $sheet->cells("A{$this->row}", function (CellWriter $cells) use ($depth) {
                $cells->setTextIndent(6 * $depth);
            });
        }

        ++$depth;
        $level->subtree->each(function ($sublevel) use ($sheet, $depth) {
            $this->buildSheet($sheet, $sublevel, $depth);
        });

        $level->cost_accounts->each(function ($cost_account) use ($sheet, $depth) {
            $sheet->row(++$this->row, [$cost_account->description, $cost_account->cost_account, $cost_account->original_boq, $cost_account->revised_boq]);

            $sheet->getRowDimension($this->row)
                ->setVisible(false)->setCollapsed(true)
                ->setOutlineLevel(min($depth + 1, 7));

            $sheet->cells("A{$this->row}", function (CellWriter $cells) use ($depth) {
                $cells->setTextIndent(6 * $depth);
            });
        });
    }
}