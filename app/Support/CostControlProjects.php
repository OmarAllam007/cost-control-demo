<?php
/**
 * Created by PhpStorm.
 * User: hazem
 * Date: 16/07/2018
 * Time: 1:07 PM
 */

namespace App\Support;


use App\ActualResources;
use App\MasterShadow;
use App\Project;

class CostControlProjects
{

    public function run()
    {
        return Project::orderBy('client_name')
            ->selectRaw('projects.*')
            ->selectRaw('(select sum(budget_cost) from break_down_resource_shadows sh where sh.project_id = projects.id and show_in_budget = 1) as latest_budget_cost')
            ->selectRaw('(select sum(cost) from actual_resources sh where sh.project_id = projects.id) as to_date_cost')
            ->selectRaw('exists(select id from break_down_resource_shadows sh where sh.project_id = projects.id and is_rollup) as has_rollup')
            ->get()->map(function ($project) {
                $project->rollup_level = '';
                if ($project->is_activity_rollup) {
                    $project->rollup_level = 'Activity';
                } elseif ($project->has_rollup) {
                    $project->rollup_level = 'Semi Activity';
                }

                return $project;
            });
    }
}