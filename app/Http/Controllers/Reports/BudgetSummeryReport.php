<?php
/**
 * Created by PhpStorm.
 * User: omar
 * Date: 17/01/17
 * Time: 08:11 ص
 */

namespace App\Http\Controllers\Reports;


use App\ActivityDivision;
use App\BreakDownResourceShadow;
use App\Project;

class BudgetSummeryReport
{
    public $projectActivities;
    public $projctDivisions;
    public $project;

    function getReport(Project $project)
    {
        $this->projectActivities = collect();
        $this->project = $project;
        $divisons = ActivityDivision::tree()->get();
        $tree = collect();
        collect(\DB::select('SELECT activity_id , SUM(budget_cost) as budget_cost 
                              FROM break_down_resource_shadows
                              WHERE project_id=' . $project->id . '
                              GROUP BY activity_id')
        )->map(function ($activity) {
            $this->projectActivities->put($activity->activity_id, $activity->budget_cost);
        });

        $total_budget = BreakDownResourceShadow::where('project_id', $project->id)->sum('budget_cost');

        foreach ($divisons as $divison) {
            $treeLevel = $this->activityDivision($divison);
            $tree [] = $treeLevel;
        }

        return view('reports.budget.budget_summery.budget_summery', compact('project', 'tree', 'total_budget'));
    }

    function activityDivision($division)
    {
        $tree = ['division_id' => $division->id, 'name' => $division->name, 'activities' => [], 'children' => [], 'total_budget' => 0];

        $divisions = ActivityDivision::whereIn('id', $division->getChildrenIds())->get();

        foreach ($divisions as $rdivision) {
            $tree['total_budget'] += $rdivision->activities->whereIn('id', $this->projectActivities->keys()->toArray())->map(function ($activity) {
                return $this->projectActivities->get($activity->id);
            })->sum();
        }

        if ($division->activities->count()) {
            $tree['activities'] = $division->activities->whereIn('id', $this->projectActivities->keys()->toArray())->map(function ($activity) {
                return ['id' => $activity->id, 'name' => $activity->name, 'budget_cost' => $this->projectActivities->get($activity->id)];
            });
        }

        if ($division->children->count()) {
            $tree['children'] = $division->children->map(function (ActivityDivision $childLevel) {
                return $this->activityDivision($childLevel);
            });
        }
        return $tree;

    }

}