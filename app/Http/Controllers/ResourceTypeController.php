<?php
namespace App\Http\Controllers;

use App\Resources;
use App\ResourceType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class ResourceTypeController extends Controller
{
    protected $rules = ['name' => 'required'];

    public function index()
    {

        $resourceTypes = ResourceType::where('id','>',7)->paginate();
        $resources = Resources::lists('id','name')->all();


        return view('resource-type.index', compact('resourceTypes','resources'));
    }

    public function create()
    {
        $resource_types =  ResourceType::lists('name','id');
        $resources = Resources::lists('name', 'id')->all();
        return view('resource-type.create', compact('resources','resource_types'));
    }
    /**
     * condition to get the id of the parent and put it as 0 if not exist
     */
    public function store(Request $request)
    {

        $this->validate($request, $this->rules);
        if (ResourceType::where('name', '=', Input::get('name'))->exists()) {

            //$parent_id = ResourceType::where('name', '=', Input::get('name'))->value('id');
           // $request->parent_id = $parent_id;
            ResourceType::create([
                'name' => $request->parent_id,
                'parent_id' => $request->name,
                'resource_id' => $request->resource_id
            ]);
        } else {
            ResourceType::create([
                'name' => $request->parent_id,
                'parent_id' => $request->name,
                'resource_id' => $request->resource_id
            ]);
            flash('Resource type has been saved', 'success');
        }
        return \Redirect::route('resource-type.index');
    }

    public function show(ResourceType $resource_type)
    {
        return view('resource-type.show', compact('resource_type'));
    }

    public function edit(ResourceType $resource_type)
    {
        return view('resource-type.edit', compact('resource_type'));
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
