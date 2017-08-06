<?php

namespace App\Reports\Budget;

use App\BreakDownResourceShadow;
use App\Project;
use App\WbsLevel;
use Illuminate\Support\Collection;

/**
* Generates WBS Report
*/
class WbsReport
{
    /** @var Project */
    protected $project;

    /** @var Collection */
    protected $wbs_levels;

    /** @var Collection */
    protected $costs;
    
    function __construct(Project $project)
    {
        $this->project = $project;
    }

    function run()
    {
        $this->wbs_levels = $this->project->wbs_levels->groupBy('parent_id');

        $this->costs = BreakDownResourceShadow::whereProjectId($this->project->id)
                ->selectRaw('wbs_id, sum(budget_cost) as cost')
                ->groupBy('wbs_id')->pluck('cost', 'wbs_id');

        $tree = $this->buildTree(0);

        return ['project' => $this->project, 'wbsTree' => $tree];
    }

    function buildTree($parent_id)
    {
        $tree = $this->wbs_levels->get($parent_id) ?: collect();

        return $tree->map(function(WbsLevel $wbs_level) {
            $wbs_level->subtree = $this->buildTree($wbs_level->id);

            $cost = $this->costs->get($wbs_level->id) ?: 0;
            $cost += $wbs_level->subtree->reduce(function ($sum, WbsLevel $level) {
                return $sum += $level->cost;
            }, $cost);

            $wbs_level->cost = $cost;

            return $wbs_level;
        });
    }
}