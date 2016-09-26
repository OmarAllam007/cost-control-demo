<?php

namespace App\Http\Controllers;


use App\Breakdown;
use App\Http\Requests\BreakdownRequest;
use App\Project;
use Illuminate\Http\Request;

class BreakdownController extends Controller
{

    public function create(Request $request)
    {
        if (!$request->has('project')) {
            return \Redirect::route('project.index');
        }

        $project = Project::find($request->get('project'));
        if (!$project) {
            flash('Project not found');
            return \Redirect::route('project.index');
        }
        return view('breakdown.create');
    }

    public function store(BreakdownRequest $request)
    {
        $breakdown = Breakdown::create($request->all());
        $breakdown->resources()->createMany($request->get('resources'));

        return \Redirect::route('project.show', $breakdown->project_id);
    }

    public function edit(Breakdown $breakdown)
    {
        return view('breakdown.edit', compact('breakdown'));
    }

    public function update(Request $request, Breakdown $breakdown)
    {

    }

    public function delete(Breakdown $breakdown)
    {

    }

    function filters(Request $request, Project $project)
    {
        $data = $request->except('_token');
        \Session::set('filters.breakdown.' . $project->id, $data);

        return \Redirect::to(route('project.show', $project) . '#breakdown');
    }
}