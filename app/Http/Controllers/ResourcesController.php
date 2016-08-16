<?php

namespace App\Http\Controllers;

use App\BusinessPartner;
use App\Resources;
use Illuminate\Http\Request;

class ResourcesController extends Controller
{

    protected $rules = ['name' => 'required'];

    public function index()
    {
        $resources = Resources::paginate();
        //$partners = BusinessPartner::all();

        return view('resources.index', compact('resources'));
    }

    public function create()
    {
        $partners = BusinessPartner::lists('name','id')->all();
        $resources = Resources::all();


        return view('resources.create',compact('partners','resources'));
    }

    public function store(Request $request)
    {
        $this->validate($request, $this->rules);

        Resources::create($request->all());

        flash('Resources has been saved', 'success');

        return \Redirect::route('resources.index');
    }

    public function show(Resources $resource)
    {
        return view('resources.show', compact('resource'));
    }

    public function edit($id)
    {

        $partners = BusinessPartner::lists('name','id')->all();
        $resource = Resources::find($id);
        return view('resources.edit', compact('resource','partners'));
    }

    public function update(Resources $resource, Request $request)
    {
        $this->validate($request, $this->rules);

        $resource->update($request->all());

        flash('Resources has been saved', 'success');

        return \Redirect::route('resources.index');
    }

    public function destroy(Resources $resource)
    {
        $resource->delete();

        flash('Resources has been deleted', 'success');

        return \Redirect::route('resources.index');
    }
}
