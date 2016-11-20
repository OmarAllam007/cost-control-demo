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

        return view('period.create', compact('project'));
    }

    function store(Request $request)
    {
        $project = Project::find(request('project'));
        if (!$project) {
            flash('Project not found');
            return \Redirect::route('project.index');
        }

        $this->doValidation($request);

        $project->periods()->create($request->all());

        return \Redirect::route('project.show', $project);
    }

    function edit(Period $period)
    {
        return view('period.edit', compact('period'));
    }

    function update(Request $request, Period $period)
    {
        $this->doValidation($request);

        $period->update($request->all());

        return \Redirect::route('project.show', $period->project);
    }

    function delete(Period $period)
    {

    }

    protected function doValidation(Request $request)
    {
        $this->validate($request, ['name' => 'required', 'start_date' => 'required|date']);
    }
}
