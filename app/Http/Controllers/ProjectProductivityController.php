<?php

namespace App\Http\Controllers;

use App\Jobs\ModifyProjectProductivityJob;
use App\Project;
use Illuminate\Http\Request;

use App\Http\Requests;

class ProjectProductivityController extends Controller
{
    function edit(Project $project)
    {
        if (cannot('productivity', $project)) {
            flash('You are not authorized to do this action');
            return \Redirect::route('project.budget', $project);
        }

        return view('project-productivity.edit', compact('project'));
    }

    function update(Project $project, Request $request)
    {
        if (cannot('productivity', $project)) {
            flash('You are not authorized to do this action');
            return \Redirect::route('project.budget', $project);
        }

        $this->validate($request, ['file' => 'required|file|mimes:xls,xlsx']);

        $result = $this->dispatchNow(new ModifyProjectProductivityJob($project, $request->file('file')));

        flash("{$result['count']} productivity has been updated", 'success');

        if ($result['failed']) {
            \Session::put('modify_productivity_result', $result);
            return \Redirect::route('project.failed-productivity', $project);
        }

        return \Redirect::route('project.budget', $project);
    }

    function show(Project $project)
    {
        if (cannot('productivity', $project)) {
            flash('You are not authorized to do this action');
            return \Redirect::route('project.budget', $project);
        }

        $result = session('modify_productivity_result');
        if (!$result) {
            return \Redirect::route('project.budget', $project);
        }

        return view('project-productivity.failed', compact('result', 'project'));
    }
}
