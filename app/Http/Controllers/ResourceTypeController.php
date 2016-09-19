<?php
namespace App\Http\Controllers;

use App\Behaviors\HasOptions;
use App\Behaviors\Tree;
use App\Resources;
use App\ResourceType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class ResourceTypeController extends Controller
{


    protected $rules = [];

    public function index()
    {

        $resource_levels = ResourceType::tree()->paginate();
        $resources = Resources::lists('id', 'name')->all();

        return view('resource-type.index', compact('resources', 'resource_levels','resource_types'));
    }

    public function create()
    {
        $resource_types = ResourceType::options();
        return view('resource-type.create', compact('resource_types'));
    }

    /**
     * condition to get the id of the parent and put it as 0 if not exist
     */
    public function store(Request $request)
    {

        $this->validate($request, $this->rules);
        ResourceType::create([
            'name' => $request->name,
            'parent_id' => $request->parent_id,
        ]);
        flash('Resource type has been saved', 'success');

        return \Redirect::route('resource-type.index');
    }

    public function show(ResourceType $resource_type)
    {

        $resource_types = ResourceType::options();
        return view('resource-type.show', compact('resource_type','resource_types'));
    }

    public function edit(ResourceType $resource_type)
    {
        $resource_types = ResourceType::options();
        return view('resource-type.edit', compact('resource_types', 'resource_type'));
    }

    public function update(ResourceType $resource_type, Request $request)
    {
        $this->validate($request, $this->rules);
        $resource_type->update($request->all());
        flash('Resource type has been saved', 'success');
        return \Redirect::route('resource-type.index');
    }

    public function destroy(ResourceType $resource_type)
    {
        $resource_type->delete();
        flash('Resource type has been deleted', 'success');
        return \Redirect::route('resource-type.index');
    }
}
