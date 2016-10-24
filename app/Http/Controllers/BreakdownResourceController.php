<?php

namespace App\Http\Controllers;

use App\BreakdownResource;
use App\Http\Requests\WipeRequest;
use App\Productivity;
use App\Project;
use App\Resources;
use Illuminate\Http\Request;

use App\Http\Requests;

class BreakdownResourceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(BreakdownResource $breakdown_resource)
    {
        return view('breakdown-resource/edit', compact('breakdown_resource'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param BreakdownResource $breakdown_resource
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, BreakdownResource $breakdown_resource)
    {
        $breakdown_resource->update($request->only(['std_activity_id', 'cost_account', 'wbs_level_id']));

        $data = $request->only(['labor_count', 'productivity_id', 'resource_qty']);
        if ($request->get('resource_qty') != $breakdown_resource->resource_qty) {
            $data['resource_qty_manual'] = 1;
        }
        $breakdown_resource->update($data);

        flash('Resource has been updated', 'success');
        return \Redirect::to(route('breakdown-resource.edit', $breakdown_resource) . '?close=1');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param BreakdownResource $breakdown_resource
     * @return \Illuminate\Http\Response
     * @internal param int $id
     */
    public function destroy(BreakdownResource $breakdown_resource)
    {
        $breakdown_resource->load('breakdown.project');
        $breakdown_resource->delete();

        flash('Resource has been deleted', 'info');
        return \Redirect::to(route('project.show', $breakdown_resource->breakdown->project) . '#breakdown');
    }

    function wipe(WipeRequest $request, Project $project)
    {

        BreakdownResource::whereIn('breakdown_id', $project->breakdowns()->pluck('id'))->delete();
        $project->breakdowns()->delete();
        Resources::where('project_id', $project->id)->delete();
        Productivity::where('project_id', $project->id)->delete();

        flash('All breakdowns have been removed', 'info');

        return \Redirect::to(route('project.show', $project) . '#breakdown');
    }
}
