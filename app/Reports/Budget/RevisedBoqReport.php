<?php

namespace App\Reports\Budget;

use App\Project;
use Illuminate\Support\Collection;

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

    }

    function sheet()
    {
        $this->run();
    }
}