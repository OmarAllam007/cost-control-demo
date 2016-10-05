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
        //send wbs_tree..
        //get divisions..
        //get activitis..
        //get Boqs
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
                    $resources[$resourceObject->id] = [
                        'id' => $resourceObject->id,
                        'name' => $resourceObject->name,
                        'type' => $rootName,
                        'budget_cost' => 0,
                        'budget_unit' => 0,
                        'unit' => $resource->project_resource->units->type,
                    ];
                }
                $total_budget_cost ='';
                $total_budget_unit = '';
                $resources[$resourceObject->id]['budget_cost'] += $resource->budget_cost;
                $resources[$resourceObject->id]['budget_unit'] += $resource->budget_unit;

                foreach ($resources as $resource){
                    $total_budget_cost += $resource['budget_cost'];
                    $total_budget_unit += $resource['budget_unit'];
                }

            }
        }


        return view('resources.manpower_report', compact('project','resources','root','total_budget_cost','total_budget_unit'));
    }


}
