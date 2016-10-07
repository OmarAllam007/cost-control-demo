<?php

namespace App\Http\Controllers;

use App\ActivityDivision;
use App\Project;
use App\Resources;
use App\StdActivity;
use App\StdActivityResource;
use Illuminate\Http\Request;

use App\Http\Requests;

class ReportController extends Controller
{
    public function wbsReport(Project $project)
    {
        return view('wbs-level.report', compact('project'));
    }

    public function productivityReport(Project $project)
    {
        return view('productivity.report', compact('project'));
    }

    public function stdActivityReport(Project $project)
    {
        $div_ids = $project->getDivisions();
        $activity_ids = $project->getActivities()->toArray();
        $all = $div_ids['all'];
        $parent_ids = $div_ids['parents'];

        $parents = ActivityDivision::whereIn('id', $parent_ids)->get();

        return view('std-activity.report', compact('parents', 'all', 'activity_ids'));
    }

    public function resourceDictionary(Project $project)
    {

        $resources_ids = $project->getProjectResources();
        $resources = Resources::whereIn('id', $resources_ids)->get();
        return view('resources.report', compact('project', 'resources'));
    }

    public function qsSummeryReport(Project $project)
    {
        $div_ids = $project->getDivisions();
        $activity_ids = $project->getActivities()->toArray();
        $all = $div_ids['all'];
        $parent_ids = $div_ids['parents'];

        $parents = ActivityDivision::whereIn('id', $parent_ids)->get();
        return view('survey.report', compact('project', 'parents', 'all', 'activity_ids'));
    }

    public function manPower(Project $project)
    {
        $resources = [];
        $root = '';

        foreach ($project->breakdown_resources as $resource) {
            $rootName = $resource->resource->resource->types->root->name;
            if (str_contains($rootName, 'LABORS')) {
                $root = $rootName;
                $resourceObject = $resource->resource->resource;
                if (!isset($resources[ $resourceObject->id ])) {
                    $resources[ $resourceObject->id ] = [
                        'id' => $resourceObject->id,
                        'name' => $resourceObject->name,
                        'type' => $rootName,
                        'budget_cost' => 0,
                        'budget_unit' => 0,
                        'unit' => $resource->project_resource->units->type,
                    ];
                }
                $total_budget_cost = '';
                $total_budget_unit = '';
                $resources[ $resourceObject->id ]['budget_cost'] += $resource->budget_cost;
                $resources[ $resourceObject->id ]['budget_unit'] += $resource->budget_unit;

                foreach ($resources as $resource) {
                    $total_budget_cost += $resource['budget_cost'];
                    $total_budget_unit += $resource['budget_unit'];
                }

            }
        }


        return view('resources.manpower_report', compact('project', 'resources', 'root', 'total_budget_cost', 'total_budget_unit'));
    }

    public function budgetSummery(Project $project)
    {
        $div_ids = $project->getDivisions();
        $activity_ids = $project->getActivities()->toArray();
        $all = $div_ids['all'];
        $parent_ids = $div_ids['parents'];

        $allDivisions = ActivityDivision::whereIn('id', $all)->get();
        $parents = ActivityDivision::whereIn('id', $parent_ids)->get();

        $std_array = $project->breakdowns()->with('std_activity')->pluck('std_activity_id')->toArray();
        $std_activities = StdActivity::whereIn('id', $std_array)->get();
        $std_activity_cost = [];
        $stds_activity_cost = [];
        $activities = [];
        $parents_cost = [];

        foreach ($allDivisions as $allDivision) {
            $childrenIds = ActivityDivision::where('parent_id', $allDivision->id)->get()->pluck('id');
            if (!isset($std_activity_cost[ $allDivision->id ])) {
                $std_activity_cost[ $allDivision->id ] = [
                    'budget_cost' => 0,
                ];
                $stds_activity_cost[ $allDivision->id ] = [
                    'name' => $allDivision->name,
                    'total_cost' => 0,
                    'divisions' => [
                        'name' => $allDivision->children,
                        'budget_cost' => 0,
                        'budget_cost' => 0,
                    ],
                ];


            }
            foreach ($std_activities->where('division_id', $allDivision->id) as $activity) {
                if (!isset($activities[ $activity->id ])) {
                    $activities[ $activity->id ] = [
                        'budget_cost' => 0,
                    ];
                }
                $activities[ $activity->id ]['budget_cost'] += $activity->getBudgetCost($project->id);
                $std_activity_cost[ $allDivision->id ]['budget_cost'] += $activities[ $activity->id ]['budget_cost'];

            }

        }


        return view('std-activity.budgetSummery', compact('parents', 'all', 'activity_ids', 'activities', 'std_activity_cost'));
    }

    public function activityResourceBreakDown(Project $project)
    {

        return view('std-activity.activity_resource_breakdown', compact('project'));
    }
}
