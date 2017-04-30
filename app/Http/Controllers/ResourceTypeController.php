<?php
namespace App\Http\Controllers;

use App\Behaviors\HasOptions;
use App\Behaviors\Tree;
use App\Http\Requests\WipeRequest;
use App\Resources;
use App\ResourceType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class ResourceTypeController extends Controller
{


    protected $rules = [];

    public function index()
    {
        if (\Gate::denies('read', 'resources')) {
            flash("You don't have access to this page");
            return \Redirect::to('/');
        }

        $resource_levels = ResourceType::tree()->with('children.resources', 'children.children.resources', 'children.children.children.resources')
            ->with('children.children.children.children.children')
            ->orderBy('name')->paginate();
        $resources = Resources::lists('id', 'name')->all();

        return view('resource-type.index', compact('resources', 'resource_levels','resource_types'));
    }

    public function create()
    {
        if (\Gate::denies('write', 'resources')) {
            flash("You don't have access to this page", 'warning');
            return \Redirect::to('/');
        }

        $resource_types = ResourceType::options();
        return view('resource-type.create', compact('resource_types'));
    }

    /**
     * condition to get the id of the parent and put it as 0 if not exist
     */
    public function store(Request $request)
    {
        if (\Gate::denies('write', 'resources')) {
            flash("You don't have access to this page");
            return \Redirect::to('/');
        }

        $this->validate($request, $this->rules);
        ResourceType::create([
            'name' => $request->name,
            'parent_id' => $request->parent_id?:0,
        ]);
        flash('Resource type has been saved', 'success');

        return \Redirect::route('resource-type.index');
    }

    public function show(ResourceType $resource_type)
    {
        if (\Gate::denies('read', 'resources')) {
            flash("You don't have access to this page");
            return \Redirect::to('/');
        }

        $resource_types = ResourceType::options();
        return view('resource-type.show', compact('resource_type','resource_types'));
    }

    public function edit(ResourceType $resource_type)
    {
        if (\Gate::denies('write', 'resources')) {
            flash("You don't have access to this page");
            return \Redirect::to('/');
        }

        $resource_types = ResourceType::options();
        return view('resource-type.edit', compact('resource_types', 'resource_type'));
    }

    public function update(ResourceType $resource_type, Request $request)
    {
        if (\Gate::denies('write', 'resources')) {
            flash("You don't have access to this page");
            return \Redirect::to('/');
        }

        $this->validate($request, $this->rules);
        $resource_type->update($request->all());
        flash('Resource type has been saved', 'success');
        return \Redirect::route('resource-type.index');
    }

    public function destroy(ResourceType $resource_type)
    {
        if (\Gate::denies('delete', 'resources')) {
            flash("You don't have access to this page");
            return \Redirect::to('/');
        }

        $resource_type->delete();
        flash('Resource type has been deleted', 'success');
        return \Redirect::route('resource-type.index');
    }

    function wipe(WipeRequest $request)
    {
        \DB::table('resource_types')->delete();
        flash('All resource types have been deleted', 'info');
        return \Redirect::route('resource-type.index');
    }
}
