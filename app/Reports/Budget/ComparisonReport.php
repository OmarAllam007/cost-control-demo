<?php

namespace App\Reports\Budget;

use App\BreakDownResourceShadow;
use App\Project;
use App\Survey;
use Illuminate\Support\Collection;
use Illuminate\Support\Fluent;
use Maatwebsite\Excel\Classes\LaravelExcelWorksheet;
use Maatwebsite\Excel\Writers\CellWriter;
use Maatwebsite\Excel\Writers\LaravelExcelWriter;

class ComparisonReport
{
    /** @var Project */
    private $project;

    /** @var int */
    private $row = 2;

    /** @var Collection */
    private $qty_surveys;

    /** @var Collection */
    private $boqs;

    /** @var Collection */
    private $wbs_levels;

    /** @var Collection */
    private $budget_costs;

    /** @var Collection */
    private $tree;

    function __construct(Project $project)
    {
        $this->project = $project;
    }

    function run()
    {
        $this->wbs_levels = $this->project->wbs_levels->groupBy('parent_id');

        $this->boqs = $this->project->boqs()->with('unit')->get()
            ->groupBy('wbs_id')->map(function ($group) {
                return $group->keyBy('cost_account');
            });;

        $this->qty_surveys = Survey::where('project_id', $this->project->id)->get()
            ->groupBy('wbs_level_id')->map(function ($group) {
                return $group->keyBy('cost_account');
            });

        $this->budget_costs = BreakDownResourceShadow::where('project_id', $this->project->id)
            ->selectRaw('wbs_id, cost_account, sum(budget_cost) as budget_cost')->groupBy(['wbs_id', 'cost_account'])
            ->get()->groupBy('wbs_level_id')->map(function ($group) {
                return $group->keyBy('cost_account');
            });

        /*$this->cost_accounts = BreakDownResourceShadow::where('project_id', $this->project->id)
            ->selectRaw('boq_id, boq_wbs_id, avg(budget_qty) as budget_qty, avg(eng_qty) as eng_qty')
            ->selectRaw('sum(budget_cost) as budget_cost, count(DISTINCT wbs_id) as num_used')
            ->groupBy(['boq_id', 'boq_wbs_id'])->get()->keyBy('boq_id');*/

        $this->tree = $this->buildTree();

        return ['project' => $this->project, 'tree' => $this->tree];
    }

    protected function buildTree($parent = 0)
    {
        return $this->wbs_levels->get($parent, collect())->map(function ($level) {
            $level->subtree = $this->buildTree($level->id);

            $childrenIds = $level->getChildrenIds();

            $level->cost_accounts = $this->boqs->get($level->id, collect())->map(function ($boq) use ($childrenIds) {
                $cost_account = new Fluent();
//                $cost_account = $this->cost_accounts->get($boq->id, new Fluent());
                $cost_account->cost_account = $boq->cost_account;
                $cost_account->description = $boq->description;
                $cost_account->item_code = $boq->item_code;
                $cost_account->price_ur = $boq->price_ur;
                $cost_account->dry_ur = $boq->dry_ur;
                $cost_account->quantity = $boq->quantity;

                $qty_survey = $this->qty_surveys->get($boq->wbs_id, collect())->get($boq->cost_account, new Fluent());
                $cost_account->budget_qty = $qty_survey->budget_qty;
                $cost_account->eng_qty = $qty_survey->eng_qty;

                $cost_account->budget_cost = $childrenIds->map(function ($wbs_id) use ($boq) {
                    return $this->budget_costs->get($wbs_id, collect())->get($boq->cost_account);
                })->sum('budget_cost');

                $cost_account->budget_price = $cost_account->budget_qty ? $cost_account->budget_cost / $cost_account->budget_qty : 0;


                $cost_account->boq_cost = $cost_account->price_ur * $cost_account->quantity;
                $cost_account->dry_cost = $cost_account->dry_ur * $cost_account->quantity;

                $cost_account->revised_boq = $cost_account->eng_qty * $cost_account->price_ur;

                $cost_account->qty_diff = ($cost_account->budget_qty - $cost_account->quantity) * $cost_account->budget_price;
                $cost_account->price_diff = ($cost_account->budget_price - $cost_account->dry_ur) * $cost_account->budget_qty;

                return $cost_account;
            })->sortBy('cost_account')->keyBy('cost_account');


//            // Append budget items that doesn't have a BOQ
//            $this->cost_accounts->where('boq_wbs_id', $level->id)->each(function ($cost_account) use ($level) {
//                if (!$level->cost_accounts->has($cost_account->boq_id)) {
//                    $cost_account->budget_qty *= $cost_account->num_used;
//                    $cost_account->eng_qty *= $cost_account->num_used;
//                    $cost_account->budget_price = $cost_account->budget_qty ? $cost_account->budget_cost / $cost_account->budget_qty : 0;
//                    $cost_account->qty_diff = ($cost_account->budget_qty - $cost_account->quantity) * $cost_account->budget_price;
//                    $cost_account->price_diff = ($cost_account->budget_price - $cost_account->dry_ur) * $cost_account->budget_qty;
//
//                    $level->cost_accounts->push($cost_account);
//                }
//            });

            $level->cost = $level->subtree->sum('cost') + $level->cost_accounts->sum('budget_cost');
            $level->boq_cost = $level->subtree->sum('boq_cost') + $level->cost_accounts->sum('boq_cost');
            $level->dry_cost = $level->subtree->sum('dry_cost') + $level->cost_accounts->sum('dry_cost');
            $level->qty_diff = $level->subtree->sum('qty_diff') + $level->cost_accounts->sum('qty_diff');
            $level->price_diff = $level->subtree->sum('price_diff') + $level->cost_accounts->sum('price_diff');
            $level->revised_boq = $level->subtree->sum('revised_boq') + $level->cost_accounts->sum('revised_boq');

            return $level;
        })->reject(function ($level) {
            return $level->subtree->isEmpty() && $level->cost_accounts->isEmpty();
        });
    }

    function excel()
    {
        \Excel::create(slug($this->project->name) . '-comparison_report', function (LaravelExcelWriter $excel) {
            $excel->sheet('Comparison Report', function ($sheet) {
                $this->sheet($sheet);
            });
            $excel->download('xlsx');
        });
    }

    function sheet(LaravelExcelWorksheet $sheet)
    {
        $this->run();

        $this->sheetHeader($sheet);

        $this->tree->each(function ($level) use ($sheet) {
            $this->sheetRow($sheet, $level);
        });

        $sheet->setFreeze('A4');
        $sheet->setAutoSize(false);
        foreach (range('A', 'Q') as $c) {
            $sheet->getColumnDimension($c)->setAutoSize(true);
        }
        $sheet->getColumnDimension('C')->setWidth(60)->setAutoSize(false);

        $sheet->setColumnFormat([
            "E3:Q{$this->row}" => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2
        ]);
    }

    private function sheetHeader(LaravelExcelWorksheet $sheet)
    {
        $sheet->mergeCells('A1:A2')->setCellValue('A1', 'WBS');
        $sheet->mergeCells('B1:B2')->setCellValue('B1', 'Cost Account');
        $sheet->mergeCells('C1:C2')->setCellValue('C1', 'Item Description');
        $sheet->mergeCells('D1:D2')->setCellValue('D1', 'Unit');

        $sheet->mergeCells('E1:G1')->setCellValue('E1', 'Client BOQ');
        $sheet->mergeCells('H1:J1')->setCellValue('H1', 'Dry Cost');
        $sheet->mergeCells('K1:N1')->setCellValue('K1', 'Budget Cost');
        $sheet->mergeCells('O1:O2')->setCellValue('O1', 'Revised BOQ');
        $sheet->mergeCells('P1:Q1')->setCellValue('P1', 'Comparison');

        $sheet->setCellValue('E2', 'Price U.R.');
        $sheet->setCellValue('F2', 'Estimated Quantity');
        $sheet->setCellValue('G2', 'Total Amount');

        $sheet->setCellValue('H2', 'Dry U.R.');
        $sheet->setCellValue('I2', 'Estimated Quantity');
        $sheet->setCellValue('J2', 'Dry Cost');

        $sheet->setCellValue('K2', 'Budget Qty');
        $sheet->setCellValue('L2', 'Eng Qty');
        $sheet->setCellValue('M2', 'Budget U.R.');
        $sheet->setCellValue('N2', 'Budget Cost');

        $sheet->setCellValue('P2', '(Budget U.R. - Dry U.R.) * Budget Qty');
        $sheet->setCellValue('Q2', '(Budget Qty - Dry Qty) * Budget U.R.)');

        $sheet->row(++$this->row, [
            "Total", '', '', '', '', '', $this->tree->sum('boq_cost'), '', '', $this->tree->sum('dry_cost'),
            '', '', '', $this->tree->sum('cost'), $this->tree->sum('revised_boq'),
            $this->tree->sum('price_diff'), $this->tree->sum('qty_diff')
        ]);

        $sheet->cells("A1:Q{$this->row}", function (CellWriter $cells) {
            $cells->setFont(['bold' => true])
                ->setAlignment('center')->setValignment('center');
        });
    }

    private function sheetRow(LaravelExcelWorksheet $sheet, $level, $depth = 0)
    {
        $sheet->row(++$this->row, [
            $level->name . " ($level->code)", '', '', '', '', '', $level->boq_cost, '', '', $level->dry_cost,
            '', '', '', $level->cost, $level->revised_boq, $level->price_diff, $level->qty_diff
        ]);

        if ($depth) {
            $sheet->getRowDimension($this->row)->setOutlineLevel(min($depth, 7))->setVisible(false)->setCollapsed(true);
            $sheet->cells("A{$this->row}", function (CellWriter $cells) use ($depth) {
                $cells->setTextIndent(6 * $depth)->setFont(['bold' => true]);
            });
        }

        $level->subtree->each(function ($sublevel) use ($sheet, $depth) {
            $this->sheetRow($sheet, $sublevel, $depth + 1);
        });

        ++$depth;
        $level->cost_accounts->each(function ($boq) use ($sheet, $depth) {
            $cells = [
                '', $boq->cost_account, $boq->description, $boq->unit->type ?? '',
                $boq->price_ur, $boq->quantity, $boq->boq_cost,
                $boq->dry_ur, $boq->quantity, $boq->dry_cost,
                $boq->budget_qty, $boq->eng_qty, $boq->budget_price, $boq->budget_cost,
                $boq->revised_boq, $boq->price_diff, $boq->qty_diff
            ];
            $sheet->row(++$this->row, $cells);
            $sheet->getRowDimension($this->row)->setOutlineLevel(min($depth, 7))->setVisible(false)->setCollapsed(true);
        });
    }
}