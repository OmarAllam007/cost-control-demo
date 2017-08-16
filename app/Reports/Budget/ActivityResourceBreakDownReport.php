<?php
/**
 * Created by PhpStorm.
 * User: hazem
 * Date: 8/14/17
 * Time: 2:54 PM
 */

namespace App\Reports\Budget;


use App\Boq;
use App\BreakdownResource;
use App\BreakDownResourceShadow;
use App\Project;
use Illuminate\Support\Collection;

class ActivityResourceBreakDownReport
{
    /** @var Collection */
    protected $tree;

    /** @var Collection */
    protected $wbs_levels;

    /** @var Project */
    protected $project;

    /** @var Collection */
    protected $boqs;

    function __construct(Project $project)
    {
        $this->project = $project;
    }

    function run()
    {
        $this->wbs_levels = $this->project->wbs_levels->groupBy('parent_id');

        $this->boqs = Boq::whereProjectId($this->project->id)->get()->keyBy('id');
//            ->groupBy('wbs_id')->map(function($group) {
//            return $group->keyBy('cost_account');
//        });

        $this->tree = $this->buildTree();

        return ['project' => $this->project, 'tree' => $this->tree];
    }

    /**
     * @param int $parent_id
     * @return Collection
     */
    function buildTree($parent_id = 0)
    {
        $tree = $this->wbs_levels->get($parent_id) ?: collect();

        return $tree->map(function ($level) {
            $level->activities = $this->buildActivities($level->id);

//            $level->boqs = $this->boqs->get($level->id)?: collect();

            $level->subtree = $this->buildTree($level->id);

            $level->cost = $level->subtree->sum('cost') +
                $level->activities->flatten()->sum('budget_cost');

            return $level;
        })->filter(function($level) {
            return $level->subtree->count() || $level->activities->count();
        });
    }

    protected function buildActivities($wbs_id)
    {
        return BreakDownResourceShadow::where('project_id', $this->project->id)
            ->where('wbs_id', $wbs_id)
            ->get()->groupBy('activity')->map(function ($group) {
                return $group->groupBy('cost_account')->map(function (Collection $resources) {
                    $cost_account = collect(['resources' => $resources, 'cost' => $resources->sum('budget_cost')]);
                    $first = $resources->first();
                    $boq = $this->boqs->get($first->boq_id);
                    $cost_account->put('boq', $boq);

                    return $cost_account;
                });
            });
    }

}