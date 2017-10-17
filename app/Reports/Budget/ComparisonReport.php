<?php

namespace App\Reports\Budget;

use App\BreakDownResourceShadow;
use App\Project;
use Illuminate\Support\Fluent;
use Maatwebsite\Excel\Writers\LaravelExcelWriter;

class ComparisonReport
{
    /** @var Project */
    private $project;

    function __construct(Project $project)
    {
        $this->project = $project;
    }

    function run()
    {
        $this->wbs_levels = $this->project->wbs_levels->groupBy('parent_id');
        $this->boqs = $this->project->boqs()->with('unit')->get()->keyBy('id')->groupBy('wbs_id');
        $this->cost_accounts = BreakDownResourceShadow::where('project_id', $this->project->id)
            ->selectRaw('boq_id, boq_wbs_id, avg(budget_qty) as budget_qty, avg(eng_qty) as eng_qty')
            ->selectRaw('sum(budget_cost) as budget_cost, sum(budget_cost) / avg(budget_qty) as budget_unit_price')
            ->selectRaw('count(DISTINCT wbs_id) as num_used')
            ->groupBy('boq_id')->get()->keyBy('boq_id');

        $this->tree = $this->buildTree();

        return ['project' => $this->project, 'tree' => $this->tree];
    }

    protected function buildTree($parent = 0)
    {

        return $this->wbs_levels->get($parent, collect())->map(function($level) {
            $level->subtree = $this->buildTree($level->id);

            $level->cost_accounts = $this->boqs->get($level->id, collect())->map(function($boq) {
                $cost_account = $this->cost_accounts->get($boq->id, new Fluent());
                $boq->budget_qty = $cost_account->budget_qty * $cost_account->num_used;
                $boq->eng_qty = $cost_account->eng_qty * $cost_account->num_used;
                $boq->budget_cost = $cost_account->budget_cost;
                $boq->budget_price = $boq->budget_qty? $boq->budget_cost / $boq->budget_qty : 0;

                $boq->boq_cost = $boq->price_ur * $boq->quantity;
                $boq->dry_cost = $boq->dry_ur * $boq->quantity;

                $boq->revised_boq = $boq->eng_qty * $boq->price_ur;

                $boq->qty_diff = ($boq->budget_qty - $boq->quantity) * $boq->budget_price;
                $boq->price_diff = ($boq->budget_price - $boq->dry_ur) * $boq->budget_qty;

                return $boq;
            })->sortBy('cost_account');

            return $level;
        })->reject(function($level) {
            return $level->subtree->isEmpty() && $level->cost_accounts->isEmpty();
        });
    }

    function excel()
    {
        \Excel::create(slug($this->project->name) .  '-comparison_report', function(LaravelExcelWriter $excel) {
            $sheet = \Closure::fromCallable([$this, 'sheet']);
            $excel->sheet('Comparison Report', $sheet);
            $excel->download('xlsx');
        });
    }

    function sheet($sheet)
    {
        $this->run();
    }
}