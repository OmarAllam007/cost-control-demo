<?php

namespace App\Http\Controllers;

use App\Boq;
use App\Jobs\BoqImportJob;
use App\Project;
use App\WbsLevel;
use Illuminate\Http\Request;

class BoqController extends Controller
{

    protected $rules = ['name' => 'required'];

    public function index()
    {
        $boqs = Boq::paginate();

        return view('boq.index', compact('boqs'));
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
        $wbsLevels = WbsLevel::tree()->paginate();

        return view('boq.create',compact('wbsLevels'));
    }

    public function store(Request $request)
    {

        Boq::create($request->all());

        flash('Boq has been saved', 'success');

        return \Redirect::route('boq.index');
    }

    public function show(Boq $boq)
    {
        return view('boq.show', compact('boq'));
    }

    public function edit(Boq $boq)
    {
        return view('boq.edit', compact('boq'));
    }

    public function update(Boq $boq, Request $request)
    {
        $boq->update($request->all());
        flash('Boq has been saved', 'success');

        return \Redirect::route('boq.index');
    }

    public function destroy(Boq $boq)
    {
        $boq->delete();

        flash('Boq has been deleted', 'success');

        return \Redirect::route('boq.index');
    }

    function import(Project $project)
    {
        return view('boq.import',compact('project'));
    }

    function postImport(Project $project,Request $request)
    {
        $this->validate($request, [
            'file' => 'required|file|mimes:xls,xlsx'
        ]);

        $file = $request->file('file');

        $this->dispatch(new BoqImportJob($project,$file->path()));

        return redirect()->route('project.show', $project);
    }
}
