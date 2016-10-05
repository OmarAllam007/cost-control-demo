<?php

namespace App\Http\Controllers;

use App\ActivityDivision;
use App\Boq;
use App\BoqDivision;
use App\Productivity;
use App\Project;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Storage;
use PHPExcel;


class ProjectController extends Controller
{


    protected $rules = ['name' => 'required'];

    public function index()
    {
        $projects = Project::paginate();

        return view('project.index', compact('projects'));
    }

    public function create()
    {
        return view('project.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, $this->rules);
        Project::create($request->all());

        flash('Project has been saved', 'success');

        return \Redirect::route('project.index');
    }

    public function show(Project $project)
    {
        set_time_limit(1800);
        ini_set('memory_limit', '32m');

        $divisions = BoqDivision::whereHas('items', function ($q) use ($project) {
            $q->where('project_id', $project->id);
        })->get();

        $project->load([
            'wbs_levels',
            'quantities',
            'breakdown_resources' => function ($q) use ($project) {
                return $q->filter(session('filters.breakdown.' . $project->id, []));
            },
            'breakdown_resources.breakdown',
            'breakdown_resources.breakdown.template',
            'breakdown_resources.breakdown.std_activity',
            'breakdown_resources.resource',
            'breakdown_resources.productivity',
        ]);

        return view('project.show', compact('project', 'divisions'));
    }

    public function edit(Project $project)
    {
        return view('project.edit', compact('project'));
    }

    public function update(Project $project, Request $request)
    {
        $this->validate($request, $this->rules);

        $project->update($request->all());

        flash('Project has been saved', 'success');

        return \Redirect::route('project.index');
    }

    public function destroy(Project $project)
    {
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



}