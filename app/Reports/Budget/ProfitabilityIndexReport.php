<?php

namespace App\Reports\Budget;

use App\BreakDownResourceShadow;
use App\BudgetRevision;
use App\Project;
use App\Revision\RevisionBreakdownResourceShadow;
use Illuminate\Support\Collection;
use Illuminate\Support\Fluent;
use Maatwebsite\Excel\Classes\LaravelExcelWorksheet;
use Maatwebsite\Excel\Writers\CellWriter;
use Maatwebsite\Excel\Writers\LaravelExcelWriter;

class ProfitabilityIndexReport
{
    /** @var Project */
    protected $project;

    /** @var Collection */
    protected $revisions;

    /** @var int  */
    protected $row = 1;

    public function __construct(Project $project)
    {
        $this->project = $project;
        $this->revisions = collect();
    }

    function run()
    {
        $first = BudgetRevision::where('project_id', $this->project->id)->orderBy('id', 'desc')->first();
        if ($first) {
            $this->revisions = BudgetRevision::where('project_id', $this->project->id)
                ->where('is_automatic', true)->orderBy('id')
                ->get()
                ->prepend($first)
                ->map(function($revision) use ($first) {
                    $revision->budget_cost = RevisionBreakdownResourceShadow::where('revision_id', $revision->id)->sum('budget_cost');
                    $revision->profitability = $revision->revised_contract_amount - $revision->budget_cost;
                    $revision->profitability_index = $revision->profitability * 100/  $revision->budget_cost;
                    $revision->variance = $revision->profitability_index - $first->profitability_index;
                    return $revision;
                });
        } else {
            $revision = new Fluent();
            $revision->name = 'Rev.00';
            $revision->budget_cost = BreakDownResourceShadow::where('project_id', $this->project->id)->sum('budget_cost');
            $revision->original_contract_amount = $this->project->project_contract_signed_value;
            $revision->change_order_amount = $this->project->change_order_amount;
            $revision->revised_contract_amount = $revision->change_order_amount + $revision->original_contract_amount;
            $revision->profitability = $revision->budget_cost - $revision->revised_contract_amount;
            $revision->profitability_index = $revision->profitability * 100/  $revision->budget_cost;

            $this->revisions->push($revision);
        }

        return ['project' => $this->project, 'revisions' => $this->revisions, 'first' => $first];
    }

    function excel()
    {
        \Excel::create(slug($this->project->name) . '-profitability_index', function (LaravelExcelWriter $excel) {
            $excel->sheet('Profitability Index', function ($sheet) {
                $this->sheet($sheet);
            });

            $excel->download('xlsx');
        });
    }

    function sheet(LaravelExcelWorksheet $sheet)
    {
        $this->run();

        $rows = [];
        $rows['names'] = $this->revisions->pluck('name')->prepend('');
        $rows['budget_costs'] = $this->revisions->pluck('budget_cost')->prepend('Budget Cost');
        $rows['contract_values'] = $this->revisions->pluck('original_contract_amount')->prepend('Original Contract Amount');
        $rows['change_order_values'] =  $this->revisions->pluck('change_order_amount')->prepend('Changer Order Amount');
        $rows['revised_values'] =  $this->revisions->pluck('revised_contract_amount')->prepend('Total Revised Contract Amount');
        $rows['profitability'] =  $this->revisions->pluck('profitability')->prepend('Profitability');
        $rows['profitability_indices'] =  $this->revisions->map(function($rev) {
            return $rev->profitability_index / 100;
        })->prepend('Profitability Index');
        $rows['variances'] =  $this->revisions->pluck('variance')->prepend('Variance');

        foreach ($rows as $row) {
            $sheet->row($this->row, $row->toArray());
            ++$this->row;
        }

        $count = $this->revisions->count() ?: 1;
        $l = chr(ord('A') + $count);

        $sheet->cells("A2:A8", function($cells) {
            $cells->setFont(['bold' => true]);
        });

        $sheet->cells("B1:{$l}1", function($cells) {
            $cells->setFont(['bold' => true])->setBackground('#3f6caf')->setFontColor('#ffffff');
        });

        $sheet->setColumnFormat([
            "B2:{$l}7" => '#,##0.00_-', "B7:{$l}8" => '0.00%'
        ]);

        foreach (range(1, 8) as $i) {
            $sheet->getRowDimension($i)->setRowHeight(30);
            $sheet->cells("A{$i}:{$l}{$i}", function ($cells) {
                $cells->setBorder('medium', 'medium', 'medium', 'medium');
            });
        }

        foreach (range('A', $l) as $c) {
            $sheet->getColumnDimension($c)->setWidth(40);
            $sheet->cells("{$c}1:{$c}8", function ($cells) {
                $cells->setBorder('medium', 'medium', 'medium', 'medium');
            });
        }

        $sheet->cells("A1:{$l}8", function(CellWriter $cells) {
            $cells->setValignment('center');
        });


        $sheet->setAutoSize(false);
    }
}