<?php

namespace App\Http\Controllers;

use App\Project;
use App\Support\Import\CostManDays;
use Illuminate\Http\Request;

use App\Http\Requests;

class CostManDaysController extends Controller
{
    public function create(Project $project)
    {
        if (cannot('actual_resource', $project)) {
            flash('You are not authorized to do this action');
            return \Redirect::route('project.cost-control', $project);
        }

        $period = $project->open_period();
        if (!$period) {
            flash('Project has no open period');
            return \Redirect::route('project.cost-control', $project);
        }

        return view('cost-man-days.create', compact('project', 'period'));
    }

    public function store(Project $project, Request $request)
    {
        if (cannot('actual_resource', $project)) {
            flash('You are not authorized to do this action');
            return \Redirect::route('project.cost-control', $project);
        }

        $period = $project->open_period();
        if (!$period) {
            flash('Project has no open period');
            return \Redirect::route('project.cost-control', $project);
        }

        $this->validate($request, ['file' => 'required|file|mimes:xlsx']);

        $importer = new CostManDays($period);
        $result = $importer->import($request->file('file')->path());

        if ($result['failed']) {
            return view('cost-man-days.failed', compact('project', 'period', 'result'));
        }

        flash("{$result['success']} Records has been imported", 'success');
        return \Redirect::route('project.productivity-index-report', $project);
    }

    public function show(Project $project)
    {
        if (cannot('actual_resource', $project)) {
            flash('You are not authorized to do this action');
            return \Redirect::route('project.cost-control', $project);
        }

        $period = $project->open_period();
        if (!$period) {
            flash('Project has no open period');
            return \Redirect::route('project.cost-control', $project);
        }
    }
}
