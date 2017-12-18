<?php

namespace App\Http\Controllers;

use App\GlobalPeriod;
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

        if (cannot('periods', $project)) {
            flash('You are not authorized to do this action');
            return \Redirect::route('project.cost-control', $project);
        }

        $period = new Period(['project_id' => $project->id]);
        $period->project = $project;
        $globalPeriods = GlobalPeriod::pluck('name', 'id');

        return view('period.create', compact('project', 'period', 'globalPeriods'));
    }

    function store(Request $request)
    {
        $project = Project::find(request('project'));
        if (!$project) {
            flash('Project not found');
            return \Redirect::route('project.index');
        }

        if (cannot('periods', $project)) {
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

        if (cannot('periods', $project)) {
            flash('You are not authorized to do this action');
            return \Redirect::route('project.cost-control', $project);
        }

        $globalPeriods = GlobalPeriod::pluck('name', 'id');

        return view('period.edit', compact('period', 'globalPeriods'));
    }

    function update(Request $request, Period $period)
    {
        $project = $period->project;

        if (cannot('periods', $project)) {
            flash('You are not authorized to do this action');
            return \Redirect::route('project.cost-control', $project);
        }

        $this->doValidation($request);

        $period->update($request->all());

        return \Redirect::route('project.cost-control', $period->project);
    }

    protected function doValidation(Request $request)
    {
        $this->validate($request, [
            'global_period_id' => 'required|exists:global_periods,id',
            'name' => 'required', 'spi_index' => 'numeric',
            'start_date' => 'required|date'
        ]);
    }
}
