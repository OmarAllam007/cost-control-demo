<?php

namespace App\Http\Controllers;

use App\Project;
use App\ProjectUser;
use Cache;
use Illuminate\Http\Request;

use App\Http\Requests;

class BudgetController extends Controller
{
    function index()
    {
        $projects = Cache::remember('projects_for_budget', 15, function () {
            return (new \App\Support\BudgetProjects())->run();
        });

        $project_ids = Project::when(!auth()->user()->is_admin, function ($q) {
            $projects = ProjectUser::where('user_id', auth()->id())->pluck('project_id');
            $q->whereIn('id', $projects)
                ->orWhere('owner_id', auth()->id())
                ->where('cost_owner_id', auth()->id());
            return $q;
        })->pluck('id');

        $projectGroups = $projects->filter(function ($project) use ($project_ids) {
            return $project_ids->contains($project->id);
        })->groupBy('client_name');

        return view('home.budget', compact('projectGroups'));
    }
}
