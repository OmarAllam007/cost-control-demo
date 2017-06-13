<?php

namespace App\Reports\Budget;

use App\Project;
use App\Revision\RevisionBreakdownResourceShadow;
use Illuminate\Support\Collection;

class BudgetTrendReport
{
    /** @var Project */
    protected $project;

    function __construct(Project $project)
    {
        $this->project = $project;
    }

    function run()
    {
        $revisions = $this->project->revisions()->pluck('name', 'id');

        $result = RevisionBreakdownResourceShadow::trendReport($this->project)->get();



        $data = collect();
        $disciplineTotals = collect();

        if ($result) {
            $disciplineTotals = RevisionBreakdownResourceShadow::disciplineTotals($this->project)->get()->groupBy('discipline')
                ->map(function(Collection $group) {
                    return $group->keyBy('revision_id');
                });

            $activityTotals = RevisionBreakdownResourceShadow::activityTotals($this->project)->get()->groupBy('activity')
                ->map(function(Collection $group) {
                    return $group->keyBy('revision_id');
                });

            $data = $result->groupBy('discipline')->map(function(Collection $group){
                return $group->groupBy('activity')->map(function(Collection $group) {
                    return $group->groupBy('resource_name')->filter(function($resources){
                        $costs = $resources->pluck('cost');
                        $firstCost = $costs->first();
                        foreach ($costs as $cost) {
                            if ($cost != $firstCost) {
                                return true;
                            }
                        }
                        return false;
                    })->map(function (Collection $group) {
                        return $group->keyBy('revision_id');
                    });
                })->filter(function ($activity) {
                    return $activity->count();
                });
            });
        }

        return [
            'project' => $this->project, 'revisions' => $revisions, 'data'=>$data,
            'disciplineTotals' => $disciplineTotals, 'activityTotals' => $activityTotals
        ];
    }
}