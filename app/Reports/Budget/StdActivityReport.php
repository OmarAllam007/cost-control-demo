<?php

namespace App\Reports\Budget;


use App\ActivityDivision;
use App\BreakDownResourceShadow;
use App\Project;
use App\StdActivity;
use Illuminate\Support\Collection;

class StdActivityReport
{
    /** @var Collection */
    protected $activity_info;

    /** @var Collection */
    protected $activities;

    /** @var Project */
    protected $project;

    /** @var Collection */
    protected $tree;

    /** @var Collection */
    protected $divisions;

    public function __construct(Project $project)
    {
        $this->project = $project;
    }

    function run()
    {
        $this->activity_info = BreakDownResourceShadow::whereProjectId($this->project->id)
            ->selectRaw('activity_id, sum(budget_cost) as budget_cost')
            ->groupBy('activity_id')
            ->orderBy('activity')
            ->pluck('budget_cost', 'activity_id');


        $this->activities = StdActivity::orderBy('name')
            ->find($this->activity_info->keys()->toArray())
            ->groupBy('division_id');

        $this->divisions = ActivityDivision::orderBy('code')->orderBy('name')
            ->get()->groupBy('parent_id');

        $this->tree = $this->buildTree();

        return ['project' => $this->project, 'tree' => $this->tree];
    }

    /**
     * @param int $parent
     * @return Collection
     */
    protected function buildTree($parent = 0)
    {
        $tree = $this->divisions->get($parent) ?: collect();

        $tree->map(function (ActivityDivision $division) {
            $division->subtree = $this->buildTree($division->id)
                ->filter(function (ActivityDivision $division) {
                    return $division->subtree->count() || $division->std_activities->count();
                });

            $division->std_activities = $this->activities->get($division->id) ?: collect();

            $cost = $division->std_activities->map(function ($activity) {
                $activity->cost = $this->activity_info->get($activity->id) ?: 0;
                return $activity;
            })->reduce(function ($sum, $activity) {
                return $sum + $activity->cost;
            }, 0);

            $division->cost = $division->subtree->reduce(function ($sum, $division) {
                return $sum + $division->cost;
            }, $cost);

            return $division;
        });

        return $tree;
    }

    function excel()
    {

    }
}