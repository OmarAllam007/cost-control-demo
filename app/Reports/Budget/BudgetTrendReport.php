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

        if ($result) {
            $data = $result->groupBy('discipline')->map(function(Collection $group){
                return $group->groupBy('activity')->map(function(Collection $group) {
                    return $group->groupBy('resource_name')->map(function (Collection $group) {
                        return $group->keyBy('revision_id');
                    });
                });
            });
        }

        return ['project' => $this->project, 'revisions' => $revisions, 'data'=>$data];
    }
}