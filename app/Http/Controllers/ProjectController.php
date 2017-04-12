<?php

namespace App\Http\Controllers;

use App\Project;
use App\ProjectUser;
use Illuminate\Http\Request;
use \Auth;


class ProjectController extends Controller
{
    protected $rules = ['name' => 'required'];

    public function index()
    {
        $projectGroups = Project::orderBy('client_name')->get()->groupBy('client_name');

        return view('project.index', compact('projectGroups'));
    }

    public function create()
    {
        if (!Auth::user()->is_admin) {
            flash('You are not authorized to do this action');
            return \Redirect::route('project.index');
        }
        return view('project.create');
    }

    public function store(Request $request)
    {
        if (!Auth::user()->is_admin) {
            flash('You are not authorized to do this action');
            return \Redirect::route('project.index');
        }
        
        $this->validate($request, $this->rules);
        $project = Project::create($request->all());
        $project->users()->sync($request->get('users', []));

        flash('Project has been saved', 'success');

        return \Redirect::route('project.index');
    }

    public function show(Project $project)
    {
        if (\Gate::denies('budget', $project) && \Gate::denies('reports', $project)) {
            flash('You are not authorized to do this action');
            return \Redirect::route('project.index');
        }

        set_time_limit(1800);
        return view('project.show', compact('project', 'divisions'));
    }

    protected function getBoqs(Project $project)
    {
        $items = [];
        foreach ($project->boqs as $boq) {
            if (!isset($items[$boq->type])) {
                $items[$boq->type] = [
                    'name' => $boq->type,
                    'items' => collect(),
                ];
            }

            $items[$boq->type]['items']->push($boq);
        }
        return $items;
    }

    public function edit(Project $project)
    {
        if (\Gate::denies('modify', $project)) {
            flash('You are not authorized to do this action');
            return \Redirect::route('project.index');
        }

        return view('project.edit', compact('project'));
    }

    public function update(Project $project, Request $request)
    {

        if (\Gate::denies('modify', $project)) {
            flash('You are not authorized to do this action');
            return \Redirect::route('project.index');
        }

        $this->validate($request, $this->rules);

        $project->update($request->all());
        $project->users()->sync($request->get('users', []));

        flash('Project has been saved', 'success');

        return \Redirect::route('project.index');
    }

    public function destroy(Project $project)
    {
        if (\Gate::denies('modify', $project)) {
            flash('You are not authorized to do this action');
            return \Redirect::route('project.index');
        }

        $project->delete();

        flash('Project has been deleted', 'success');

        return \Redirect::route('project.index');
    }

    function filters(Request $request, Project $project)
    {
        $data = $request->all();
        \Session::set('filters.breakdown.' . $project->id, $data);

        return back();
    }


    function costControl(Project $project)
    {
        if (\Gate::denies('cost_control', $project) && \Gate::denies('reports', $project)) {
            flash('You are not authorized to do this action');
            return \Redirect::route('project.index');
        }

        $period = $project->open_period();
        return view('project.cost-control', compact('project', 'period'));
    }

    function duplicate(Project $project, Request $request)
    {
        if ($request->has('name')) {
            $name = $request->get('name');
        } else {
            $name = 'Copy of ' . $project->name;
        }

        $id = $project->duplicate($name);

        flash('Project has been duplicated', 'success');
        return \Redirect::route('project.budget', $id);
    }
}