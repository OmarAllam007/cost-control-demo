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
        $this->project = $project;
        $divisons = ActivityDivision::tree()->get();
        $tree = collect();
        $this->projectActivities = BreakDownResourceShadow::where('project_id', $project->id)
            ->get()
            ->pluck('activity_id')
            ->toArray();

        $total_budget =BreakDownResourceShadow::where('project_id', $project->id)
            ->get()->sum('budget_cost');

        foreach ($divisons as $divison) {
            $treeLevel = $this->activityDivision($divison);
            $tree [] = $treeLevel;
        }

        return view('reports.budget.budget_summery.budget_summery', compact('project', 'tree','total_budget'));
    }

    function activityDivision($division)
    {
        $tree = ['division_id' => $division->id, 'name' => $division->name, 'activities' => [],'children'=>[], 'total_budget' => 0];

        $divisions = ActivityDivision::whereIn('id', $division->getChildrenIds())->get();
        foreach ($divisions as $rdivision) {
            $tree['total_budget'] += $rdivision->activities->whereIn('id', $this->projectActivities)->map(function ($activity)  {
                return BreakDownResourceShadow::where('project_id', $this->project->id)
                    ->where('activity_id', $activity->id)->get()->sum('budget_cost');
            })->sum();
        }

        if ($division->children->count()) {
            $tree['children'] = $division->children->map(function (ActivityDivision $childLevel) {
                return $this->activityDivision($childLevel);
            });
        }

        if ($division->activities->count()) {
            $tree['activities'] = $division->activities->whereIn('id', $this->projectActivities)->map(function ($activity) {
                return ['id' => $activity->id, 'name' => $activity->name, 'budget_cost' => BreakDownResourceShadow::where('project_id', $this->project->id)
                    ->where('activity_id', $activity->id)->sum('budget_cost')];
            });
        }
        return $tree;

    }

}