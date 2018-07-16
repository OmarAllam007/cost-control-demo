<?php

namespace App\Http\Controllers;

use App\Project;
use App\ProjectUser;
use Cache;
use Illuminate\Http\Request;

use App\Http\Requests;

class CostControlController extends Controller
{
    function index()
    {
        $project_ids = Project::orderBy('client_name')->when(!auth()->user()->is_admin, function($q) {
            $projects = ProjectUser::where('user_id', auth()->id())->where('cost_control', 1)->pluck('project_id');
            $q->whereIn('id', $projects)->orWhere('owner_id', auth()->id())->orWhere('cost_owner_id', auth()->id());
            return $q;
        })->pluck('id');

        $projects = Cache::remember('projects_for_cost_control', 15, function () {
            return  (new \App\Support\CostControlProjects)->run();
        })->filter(function ($project) use ($project_ids) {
            return $project_ids->contains($project->id);
        });

        $projectGroups = $projects->groupBy('client_name');

        return view('home.cost-control', compact('projectGroups'));
    }
}
