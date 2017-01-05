<?php

namespace App\Http\Controllers;

use App\Period;
use App\Project;
use Illuminate\Http\Request;

class PeriodController extends Controller
{
    function create()
    {
        $project = Project::find(request('project'));
        if (!$project) {
            flash('Project not found');
            return \Redirect::route('project.index');
        }

        if (cannot('period', $project)) {
            flash('You are not authorized to do this action');
            return \Redirect::route('project.cost-control', $project);
        }

        return view('period.create', compact('project'));
    }

    function store(Request $request)
    {
        $project = Project::find(request('project'));
        if (!$project) {
            flash('Project not found');
            return \Redirect::route('project.index');
        }

        if (cannot('period', $project)) {
            flash('You are not authorized to do this action');
            return \Redirect::route('project.cost-control', $project);
        }

        $this->doValidation($request);

        $project->periods()->create($request->all());

        return \Redirect::route('project.cost-control', $project);
    }

    function edit(Period $period)
    {
        $project = $period->project;

        if (cannot('period', $project)) {
            flash('You are not authorized to do this action');
            return \Redirect::route('project.cost-control', $project);
        }

        return view('period.edit', compact('period'));
    }

    function update(Request $request, Period $period)
    {
        $project = $period->project;

        if (cannot('period', $project)) {
            flash('You are not authorized to do this action');
            return \Redirect::route('project.cost-control', $project);
        }

        $this->doValidation($request);

        $period->update($request->all());

        return \Redirect::route('project.cost-control', $period->project);
    }

    protected function doValidation(Request $request)
    {
        $this->validate($request, ['name' => 'required', 'start_date' => 'required|date']);
    }
}
