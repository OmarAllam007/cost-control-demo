<?php

namespace App\Http\Controllers;

use App\BusinessPartner;
use App\Jobs\ResourcesImportJob;
use App\Project;
use App\Resources;
use App\ResourceType;
use App\Unit;
use Illuminate\Http\Request;

class ResourcesController extends Controller
{

    protected $rules = ['name' => 'required'];

    public function index()
    {
        $resources = Resources::paginate(50);
        return view('resources.index', compact('resources'));
    }

    public function create(Request $request)
    {

        $units_drop = Unit::options();
        $partners = BusinessPartner::options();
        $resources = Resources::all();
        $resource_types = ResourceType::lists('name', 'id')->all();
        $edit = false;

        return view('resources.create', compact('partners', 'resources', 'resource_types', 'units_drop', 'edit'));
    }

    public function store(Request $request)
    {
        $this->validate($request, $this->rules);
        if ($request['waste'] <= 1)
            $request['waste'] = $request->waste;
        else
            $request['waste'] = ($request->waste / 100);

        Resources::create($request->all());

        flash('Resource has been saved', 'success');

        return \Redirect::route('resources.index');
    }

    public function show(Resources $resource)
    {
        return view('resources.show', compact('resource'));
    }

    public function edit(Resources $resources)
    {

        $partners = BusinessPartner::options();
        $resource_types = ResourceType::lists('name', 'id')->all();
        $units_drop = Unit::options();
        $edit = true;

        return view('resources.edit', compact('resources', 'partners', 'resource_types', 'units_drop', 'edit'));
    }

    public function update(Resources $resources, Request $request)
    {
        $this->validate($request, $this->rules);

        if ($request['waste'] <= 1)
            $request['waste'] = $request->waste;
        else
            $request['waste'] = ($request->waste / 100);

        $resources->update($request->all());

        flash('Resource has been saved', 'success');

        return \Redirect::route('resources.index');
    }

    public function destroy(Resources $resources)
    {
        $resources->delete();

        flash('Resources has been deleted', 'success');

        return \Redirect::route('resources.index');
    }

    function import()
    {
        return view('resources.import');
    }

    function postImport(Request $request)
    {
        $this->validate($request, [
            'file' => 'required|file|mimes:xls,xlsx',
        ]);

        $file = $request->file('file');

        $this->dispatch(new ResourcesImportJob($file->path()));

        flash('Resource have been imported', 'success');
        return redirect()->route('resources.index');
    }

    function override(Resources $resources, Project $project)
    {
        $overwrote = Resources::version($project->id, $resources->id)->first();

        if (!$overwrote) {
            $overwrote = $resources;
        }

        return view('resources.override', ['resource' => $overwrote, 'baseResource' => $resources, 'project' => $project]);
    }

    function postOverride(Resources $resources, Project $project, Request $request)
    {
        $this->validate($request, $this->rules);

        $newResource = Resources::version($project->id, $resources->id)->first();

        if (!$newResource) {
            $newResource = new Resources($request->all());
            $newResource->project_id = $project->id;
            $newResource->resource_id = $resources->id;
            $newResource->save();
        } else {
            $newResource->update($request->all());
        }

        flash('Resource has been updated successfully', 'success');
        return redirect()->route('project.show', $project);
    }
}
