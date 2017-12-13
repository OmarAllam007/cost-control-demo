<?php
/**
 * Created by PhpStorm.
 * User: omar
 * Date: 27/12/16
 * Time: 11:19 ص
 */

namespace App\Http\Controllers\Reports\CostReports;

use App\MasterShadow;
use App\Period;
use App\Project;
use App\WbsLevel;
use Illuminate\Support\Collection;
use Illuminate\Support\Fluent;

class OverdraftReport
{

    /** @var Project */
    protected $project;

    /** @var Collection  */
    protected $wbs_levels;

    /** @var Collection  */
    protected $rawData;

    /** @var Collection  */
    protected $tree;

    /** @var Fluent */
    private $totals;

    public function __construct(Period $period)
    {
        $this->period = $period;
        $this->project = $period->project;
    }

    public function run()
    {
        $this->wbs_levels = $this->project->wbs_levels->groupBy('parent_id');
        $this->rawData = MasterShadow::overDraftReport($this->period)->get()->groupBy('wbs_id');;

        $this->tree = $this->buildTree();

        $periods = $this->project->periods()->readyForReporting()->pluck('name', 'id');

        $this->totals = new Fluent([
            'physical_revenue' => $this->tree->sum('physical_revenue'),
            'physical_revenue_upv' => $this->tree->sum('physical_revenue_upv'),
            'actual_revenue' => $this->tree->sum('actual_revenue'),
            'var' => $this->tree->sum('var'),
            'var_upv' => $this->tree->sum('var_upv'),
        ]);

        return view('reports.cost-control.over-draft.over_draft', [
            'tree' => $this->tree,
            'period' => $this->period,
            'project' => $this->project,
            'periods' => $periods,
            'totals' => $this->totals
        ]);
    }

    protected function buildTree($parent = 0)
    {

        return $this->wbs_levels->get($parent, collect())->map(function($level) {
            $level->subtree = $this->buildTree($level->id);
            $level->boqs = $this->rawData->get($level->id, collect())->map(function ($boq) {
                $boq->var = $boq->actual_revenue - $boq->physical_revenue;
                $boq->var_upv = $boq->actual_revenue - $boq->physical_revenue_upv;
                return $boq;
            });

            $level->var = $level->subtree->sum('var') + $level->boqs->sum('var');
            $level->var_upv = $level->subtree->sum('var_upv') + $level->boqs->sum('var_upv');
            $level->physical_revenue = $level->subtree->sum('physical_revenue') + $level->boqs->sum('physical_revenue');
            $level->physical_revenue_upv = $level->subtree->sum('physical_revenue_upv') + $level->boqs->sum('physical_revenue_upv');
            $level->actual_revenue = $level->subtree->sum('actual_revenue') + $level->boqs->sum('actual_revenue');

            return $level;
        })->reject(function($level) {
            return $level->subtree->isEmpty() && $level->boqs->isEmpty();
        });
    }
}