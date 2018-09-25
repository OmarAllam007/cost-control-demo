<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Project;
use App\ProjectUser;
use Cache;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('home.index');
    }

    function acknowledgement()
    {
        return view('home.acknowledgement');
    }

    function comingSoon()
    {
        return view('home.coming-soon');
    }

    function reports()
    {
        $projects = Cache::remember('projects_for_cost_control', 15, function () {
            return (new \App\Support\CostControlProjects())->run();
        });

        $project_ids = Project::when(!auth()->user()->is_admin, function ($q) {
            $projects = ProjectUser::where('user_id', auth()->id())->where(function($q) {
                $q->where('reports', 1)->orWhere('cost_reports', 1);
            })->pluck('project_id');

            $q->whereIn('id', $projects)
                ->orWhere('owner_id', auth()->id())
                ->where('cost_owner_id', auth()->id());

            return $q;
        })->pluck('id');

        $projectGroups = $projects->filter(function ($project) use ($project_ids) {
            return $project_ids->contains($project->id);
        })->groupBy('client_name');

        return view('home.reports', compact('projectGroups'));
    }

    function masterData()
    {
        return view('home.master-data');
    }
}
