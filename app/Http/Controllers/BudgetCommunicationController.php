<?php

namespace App\Http\Controllers;

use App\Project;
use App\ProjectRole;
use Illuminate\Http\Request;

class BudgetCommunicationController extends Controller
{
    function create(Project $project)
    {
        $roles = ProjectRole::where('project_id', $project->id)->with('role', 'role.reports')->get();

        return view('communication.budget', compact('project', 'roles'));
    }

    function store(Project $project, Request $request)
    {

    }
}
