<?php

namespace App\Http\Controllers;

use App\Project;
use App\ProjectRole;
use App\Role;
use Illuminate\Http\Request;

use App\Http\Requests;

class ProjectRolesController extends Controller
{
    function edit(Project $project)
    {
        if (!can('budget_owner', $project) && !can('cost_owner', $project)) {
            flash('You are not authotized to do this action');
            return \Redirect::route('project.show', $project);
        }

        $roles = Role::with('reports')->get();

        return view('project-roles.edit', compact('project','roles'));
    }

    function update(Project $project, Request $request)
    {
        if (!can('budget_owner', $project) && !can('cost_owner', $project)) {
            flash('You are not authotized to do this action');
            return \Redirect::route('project.show', $project);
        }

        $this->validate($request, config('validation.project_roles'));

        ProjectRole::updateRoles($project, $request->get('roles'));

        return \Redirect::to($request->get('back', route('project.show', $project)));
    }
}
