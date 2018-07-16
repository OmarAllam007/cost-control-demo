<?php
/**
 * Created by PhpStorm.
 * User: hazem
 * Date: 16/07/2018
 * Time: 10:41 AM
 */

namespace App\Support;


use App\Project;
use App\Revision\RevisionBreakdownResourceShadow;

class BudgetProjects
{
    function run()
    {
        /*
          ->when(!auth()->user()->is_admin, function ($q) {
            $projects = ProjectUser::where('user_id', auth()->id())->pluck('project_id');
            $q->whereIn('id', $projects)->orWhere('owner_id', auth()->id());
            return $q;
        })
         */
        return Project::orderBy('client_name')
            ->selectRaw('projects.*, (select sum(budget_cost) from break_down_resource_shadows sh where sh.project_id = projects.id) as latest_budget_cost')
            ->get()->map(function (Project $project) {
                $revision = $project->revisions()->oldest('id')->first();
                $project->original_budget_cost = RevisionBreakdownResourceShadow::query()
                    ->where('project_id', $project->id)->sum('budget_cost');
                return $project;
            });
    }
}