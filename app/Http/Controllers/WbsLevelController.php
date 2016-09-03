<?php

namespace App\Http\Controllers;

use App\Jobs\WbsImportJob;
use App\Project;
use App\WbsLevel;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class WbsLevelController extends Controller
{

    protected $rules = ['name' => 'required'];

    public function index()
    {
        $wbsLevels = WbsLevel::tree()->paginate();

        return view('wbs-level.index', compact('wbsLevels'));
    }

    public function create(Request $request)
    {
        if (!$request->has('project')) {
            flash('Project not found');
            return redirect()->route('project.index');
        } else {
            $project = Project::find($request->get('project'));
            if (!$project) {
                flash('Project not found');
                return redirect()->route('project.index');
            }
        }

        return view('wbs-level.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, $this->rules);

        $wbs_level = WbsLevel::create($request->all());

        flash('WBS level has been saved', 'success');

        return \Redirect::route('project.show', $wbs_level->project_id);
    }

    public function show(WbsLevel $wbs_level)
    {
        return view('wbs-level.show', compact('wbs_level'));
    }

    public function edit(WbsLevel $wbs_level)
    {
        return view('wbs-level.edit', compact('wbs_level'));
    }

    public function update(WbsLevel $wbs_level, Request $request)
    {
        $this->validate($request, $this->rules);

        $wbs_level->update($request->all());

        flash('WBS level has been saved', 'success');

        return \Redirect::route('project.show', $wbs_level->project_id);
    }

    public function destroy(WbsLevel $wbs_level)
    {
        $wbs_level->delete();

        flash('WBS level has been deleted', 'success');

        return \Redirect::route('project.show', $wbs_level->project_id);
    }

    function import(Project $project)
    {
        return view('wbs-level.import', compact('project'));
    }

    function postImport(Project $project, Request $request)
    {
        $this->validate($request, [
            'file' => 'required|file|mimes:xls,xlsx'
        ]);

        $file = $request->file('file');

        $this->dispatch(new WbsImportJob($project, $file->path()));

        flash('WBS has been imported to ' . $project->name, 'success');
        return redirect()->route('project.show', $project);
    }
}
