<?php

namespace App\Http\Controllers;

use App\Http\Requests\WipeRequest;
use App\Jobs\CacheWBSTreeInQueue;
use App\Jobs\Export\WbsLevelExportJob;
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
        abort(404);
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

            if (\Gate::denies('wbs', $project)) {
                flash('You are not authorized to do this action');
                return \Redirect::route('project.index');
            }
        }

        return view('wbs-level.create');
    }

    public function store(Request $request)
    {
        if (!$request->has('project_id')) {
            flash('Project not found');
            return \Redirect::route('project.index');
        } else {
            $project = Project::find($request->get('project_id'));
            if (!$project) {
                flash('Project not found');
                return \Redirect::route('project.index');
            }

            if (\Gate::denies('wbs', $project)) {
                flash('You are not authorized to do this action');
                return \Redirect::route('project.index');
            }
        }

        $this->validate($request, $this->rules);

        $wbs_level = WbsLevel::create($request->all());

        flash('WBS level has been saved', 'success');

//        return \Redirect::route('project.show', $wbs_level->project_id);
        return \Redirect::to('/blank?reload=wbs');
    }

    public function show(WbsLevel $wbs_level)
    {
        abort(404);
        return view('wbs-level.show', compact('wbs_level'));
    }

    public function edit(WbsLevel $wbs_level)
    {
        if (\Gate::denies('wbs', $wbs_level->project)) {
            flash('You are not authorized to do this action');
            return \Redirect::route('project.index');
        }

        return view('wbs-level.edit', compact('wbs_level'));
    }

    public function update(WbsLevel $wbs_level, Request $request)
    {
        if (\Gate::denies('wbs', $wbs_level->project)) {
            flash('You are not authorized to do this action');
            return \Redirect::route('project.index');
        }
        
        $this->validate($request, $this->rules);

        $wbs_level->update($request->all());

        flash('WBS level has been saved', 'success');

        return \Redirect::to('/blank?reload=wbs');
    }

    public function destroy(WbsLevel $wbs_level, Request $request)
    {
        if (\Gate::denies('wbs', $wbs_level->project)) {
            $msg = 'You are not authorized to do this action';
            if ($request->ajax()) {
                return ['ok' => false, 'message' => $msg];
            }

            flash($msg);
            return \Redirect::route('project.index');
        }
        
        $wbs_level->deleteRecursive();
        $this->dispatch(new CacheWBSTreeInQueue($wbs_level->project));

        $msg = 'WBS level has been deleted';
        if ($request->ajax()) {
            return ['ok' => true, 'message' => $msg];
        }

        flash($msg, 'info');
        return \Redirect::route('project.show', $wbs_level->project_id);
    }

    function import(Project $project)
    {
        if (\Gate::denies('wbs', $project)) {
            flash('You are not authorized to do this action');
            return \Redirect::route('project.index');
        }
        
        return view('wbs-level.import', compact('project'));
    }

    function postImport(Project $project, Request $request)
    {
        if (\Gate::denies('wbs', $project)) {
            flash('You are not authorized to do this action');
            return \Redirect::route('project.index');
        }
        
        $this->validate($request, [
            'file' => 'required|file|mimes:xls,xlsx',
        ]);

        $file = $request->file('file');

        $count = $this->dispatch(new WbsImportJob($project, $file->path()));

        flash($count . 'WBS has been imported', 'success');
//        return redirect()->to(route('project.show', $project) . '#wbs-structure');
        return \Redirect::to('/blank?reload=wbs');
    }

    public function exportWbsLevels(Project $project)
    {
        if (\Gate::allows('budget', $project) || \Gate::allows('cost_control', $project)) {
            return $this->dispatch(new WbsLevelExportJob($project));
        }

        flash('You are not authorized to do this action');
        \Redirect::route('project.index');
    }

    function wipe(WipeRequest $request, Project $project)
    {
        $project->quantities()->delete();
        $project->boqs()->delete();
        $project->breakdowns()->delete();
        foreach ($project->wbs_levels as $level) {
            $level->deleteRelations();
            $level->delete();
        }

        \Cache::forget('wbs-tree-' . $project->id);

        $msg = 'WBS has been deleted';
        if ($request->ajax()) {
            return ['ok' => true, 'message' => $msg];
        }
        flash($msg, 'info');

        return \Redirect::route('project.show', $project);
    }

}
