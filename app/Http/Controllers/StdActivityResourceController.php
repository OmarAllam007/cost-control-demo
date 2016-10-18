<?php

namespace App\Http\Controllers;

use App\StdActivityResource;
use Illuminate\Http\Request;

class StdActivityResourceController extends Controller
{

    protected $rules = ['template_id' => 'required', 'resource_id' => 'required', 'equation' => 'required'];

    public function index()
    {
        $stdActivityResources = StdActivityResource::paginate();

        return view('std-activity-resource.index', compact('stdActivityResources'));
    }

    public function create()
    {
        return view('std-activity-resource.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, $this->rules);

        $resource = StdActivityResource::create($request->all());

        $resource->syncVariables($request->get('variables'));

        flash('Std activity resource has been saved', 'success');

        return \Redirect::route('breakdown-template.show', $resource->template);
    }

    public function show(StdActivityResource $std_activity_resource)
    {
        return view('std-activity-resource.show', compact('std_activity_resource'));
    }

    public function edit(StdActivityResource $std_activity_resource)
    {
        return view('std-activity-resource.edit', compact('std_activity_resource'));
    }

    public function update(StdActivityResource $std_activity_resource, Request $request)
    {
        $this->validate($request, $this->rules);

        $std_activity_resource->update($request->all());
        $std_activity_resource->syncVariables($request->get('variables'));

        flash('Std activity resource has been saved', 'success');

        return \Redirect::route('breakdown-template.show', $std_activity_resource->template);
    }

    public function destroy(StdActivityResource $std_activity_resource)
    {
        $std_activity_resource->delete();

        flash('Std activity resource has been deleted', 'success');

        return \Redirect::route('breakdown-template.show', $std_activity_resource->template);
    }
}
