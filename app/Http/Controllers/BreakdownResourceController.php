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

    public function edit(BreakdownResource $breakdown_resource)
    {
        return view('breakdown-resource/edit', compact('breakdown_resource'));
    }


    public function update(Request $request, BreakdownResource $breakdown_resource)
    {
        $breakdown_resource->breakdown->update($request->only(['std_activity_id', 'cost_account', 'wbs_level_id']));
        $breakdown_resource->update($request->only('labor_count', 'productivity_id', 'resource_id', 'equation'));

        flash('Resource has been updated', 'success');
//        return \Redirect::to(route('breakdown-resource.edit', $breakdown_resource) . '?close=1');
        return \Redirect::to('/blank?reload=breakdown');
    }

    public function destroy(BreakdownResource $breakdown_resource, Request $request)
    {
        $breakdown_resource->load('breakdown.project');
        $breakdown_resource->delete();

        $msg = 'Resource has been deleted';
        if ($request->ajax()) {
            return ['ok' => true, 'message' => $msg];
        }

        flash($msg, 'info');
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
