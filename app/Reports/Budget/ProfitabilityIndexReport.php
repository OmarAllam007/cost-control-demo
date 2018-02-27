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
        $first = BudgetRevision::where('project_id', $this->project->id)->orderBy('id')->first();
        if ($first) {
            $this->revisions = BudgetRevision::where('project_id', $this->project->id)
                ->where('id', '<>', $first->id)
                ->orderBy('id')
                ->get()
                ->prepend($first)
                ->map(function($revision) use ($first) {
                    $revision->variance = $revision->planned_profitability_index - $first->planned_profitability_index;
                    return $revision;
                });
        } else {
            $revision = new Fluent();
            $revision->name = 'Rev.00';
            $revision->project = $this->project;
            $revision->budget_cost = $this->project->budget_cost;
            $revision->eac_contract_amount = $this->project->eac_contact_amount;
            $revision->planned_profit_amount = $this->project->planned_profit_amount;
            $revision->planned_profitability_index = $this->project->planned_profitability;

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
      /*
       Original Signed Contract Value
EAC Contract Amount
Total Budget Cost
Planned Profit Amount
Planned Profitability Index
Variance
       */

        $count = $this->revisions->count();
        $contract_value = $this->project->project_contract_signed_value;

        $rows[1] = $this->revisions->pluck('name')->prepend('Item');
        $rows[2] = collect(array_fill(0, $count, $contract_value))->prepend('Original Signed Contract Value');
        $rows[3] = $this->revisions->pluck('eac_contract_amount')->prepend('EAC Contract Amount');
        $rows[4] = $this->revisions->pluck('budget_cost')->prepend('Total Budget Cost');
        $rows[5] =  $this->revisions->pluck('planned_profit_amount')->prepend('Planned Profit Amount');
        $rows[6] =  $this->revisions->map(function($rev) {
            return $rev->planned_profitability_index / 100;
        })->prepend('Planned Profitability Index');
        $rows[7] =  $this->revisions->map(function($rev) {
            return $rev->variance / 100;
        })->prepend('Variance');

        foreach ($rows as $row) {
            $sheet->row($this->row, $row->toArray());
            ++$this->row;
        }

        $count = $this->revisions->count() ?: 1;
        $l = chr(ord('A') + $count);

        $sheet->cells("A2:A7", function($cells) {
            $cells->setFont(['bold' => true]);
        });

        $sheet->cells("B1:{$l}1", function($cells) {
            $cells->setFont(['bold' => true])->setBackground('#3f6caf')->setFontColor('#ffffff');
        });

        $sheet->setColumnFormat([
            "B2:{$l}5" => '#,##0.00_-', "B6:{$l}7" => '0.00%'
        ]);

        foreach (range(1, 7) as $i) {
            $sheet->getRowDimension($i)->setRowHeight(30);
            $sheet->cells("A{$i}:{$l}{$i}", function ($cells) {
                $cells->setBorder('medium', 'medium', 'medium', 'medium');
            });
        }

        foreach (range('A', $l) as $c) {
            $sheet->getColumnDimension($c)->setWidth(40);
            $sheet->cells("{$c}1:{$c}7", function ($cells) {
                $cells->setBorder('medium', 'medium', 'medium', 'medium');
            });
        }

        $sheet->cells("A1:{$l}7", function(CellWriter $cells) {
            $cells->setValignment('center');
        });


        $sheet->setAutoSize(false);
    }
}