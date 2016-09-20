<?php

namespace App\Http\Controllers;

use App\ActivityDivision;
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
        ini_set('memory_limit', '4G');
        $project->load(['wbs_levels','breakdown_resources']);
        return view('project.show', compact('project','productivity'));
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


}