<?php

namespace App\Http\Controllers;

use App\BusinessPartner;
use App\Resources;
use App\ResourceType;
use App\Unit;
use Illuminate\Http\Request;

class ResourcesController extends Controller
{

    protected $rules = ['name' => 'required'];

    public function index()
    {
        $resources = Resources::paginate();
       // $partners = BusinessPartner::paginate();
          return view('resources.index', compact('resources'));
    }

    public function create()
    {
        $units_drop = Unit::lists('type', 'id')->all();
        $partners = BusinessPartner::lists('name','id')->all();
        $resources = Resources::all();
        $resource_types =  ResourceType::lists('name','id')->all();


        return view('resources.create',compact('partners','resources','resource_types','units_drop'));
        return view('resources.create',compact('partners','resources','resource_types','units_drop'));
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

    public function edit(Resources $resources)
    {

        $partners = BusinessPartner::lists('name','id')->all();
        $resource_types =  ResourceType::lists('name','id')->all();

        return view('resources.edit', compact('resources','partners','resource_types'));
    }

    public function update(Resources $resources, Request $request)
    {
        $this->validate($request, $this->rules);

        $resources->update($request->all());

        flash('Resources has been saved', 'success');

        return \Redirect::route('resources.index');
    }

    public function destroy(Resources $resources)
    {
        $resources->delete();

        flash('Resources has been deleted', 'success');

        return \Redirect::route('resources.index');
    }
}
