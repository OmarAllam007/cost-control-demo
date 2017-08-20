<?php
/**
 * Created by PhpStorm.
 * User: hazem
 * Date: 8/17/17
 * Time: 3:56 PM
 */

namespace App\Reports\Budget;


use App\Boq;
use App\BreakDownResourceShadow;
use App\Project;
use App\WbsLevel;
use Illuminate\Support\Collection;
use Symfony\Component\Debug\Exception\FatalThrowableError;

class BudgetCostDryCostByBuildingReport
{
    /** @var Project */
    protected $project;

    /** @var Collection */
    protected $tree;

    /** @var Collection */
    protected $wbs_levels;

    /** @var Collection */
    protected $info;

    /** @var Collection */
    protected $budget_costs;

    /** @var Collection */
    protected $boqs;

    public function __construct(Project $project)
    {
        $this->project = $project;
    }

    function run()
    {
        $this->wbs_levels = $this->project->wbs_levels->sortBy('name')->groupBy('parent_id');

        $this->budget_costs = BreakDownResourceShadow::where('project_id', $this->project->id)
            ->selectRaw('boq_wbs_id as wbs_id, sum(budget_cost) as cost')
            ->groupBy('boq_wbs_id')->get()->keyBy('wbs_id');

        $this->boqs = Boq::whereProjectId($this->project->id)
            ->selectRaw('wbs_id, sum(boqs.quantity * boqs.dry_ur) as dry_cost')
            ->groupBy('wbs_id')->get()->keyBy('wbs_id');

        $this->tree = $this->buildTree();

        return ['project' => $this->project, 'tree' => $this->tree];
    }

    function buildTree($parent_id = 0)
    {
        $tree = $this->wbs_levels->get($parent_id) ?: collect();

        return $tree->map(function (WbsLevel $level) {
            $level->subtree = $this->buildTree($level->id);

            $dry_cost = $this->boqs->get($level->id)->dry_cost ?? 0;
            $budget_cost = $this->budget_costs->get($level->id)->cost ?? 0;

            $level->dry_cost = $dry_cost + $level->subtree->sum('dry_cost');
            $level->cost = $budget_cost + $level->subtree->sum('cost');

            $level->difference = $level->cost - $level->dry_cost;
            $level->increase = $level->dry_cost? $level->difference * 100 / $level->dry_cost : 0;

            return $level;
        })->filter(function ($level) {
            return $this->boqs->has($level->id) || $level->subtree->count();
        });
    }

    function excel()
    {

    }

}