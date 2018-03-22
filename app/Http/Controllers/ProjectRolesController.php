<?php

namespace App\Http\Controllers;

use App\Project;
use App\ProjectRole;
use App\Role;
use Illuminate\Http\Request;

class ProjectRolesController extends Controller
{
    function edit(Project $project)
    {
        if (!can('budget_owner', $project) && !can('cost_owner', $project)) {
            flash('You are not authotized to do this action');
            return \Redirect::route('project.show', $project);
        }

        $roles = Role::all()->map(function($role) use ($project) {
            $role->users = old("roles.{$role->id}.users");
            if (!$role->users) {
                $role->users = ProjectRole::where('project_id', $project->id)->where('role_id', $role->id)->get();
            }

            $role->enabled = count($role->users);

            return $role;
        });



        return view('project-roles.edit', compact('project','roles'));
    }

    function update(Project $project, Request $request)
    {
        if (!can('budget_owner', $project) && !can('cost_owner', $project)) {
            flash('You are not authorized to do this action');
            return \Redirect::route('project.show', $project);
        }

        $this->validate($request, config('validation.project_roles'));

        ProjectRole::updateRoles($project, $request->get('roles'));

        flash('Project communication plan has been saved', 'success');

        return \Redirect::route('project.roles', $project);
    }
}
