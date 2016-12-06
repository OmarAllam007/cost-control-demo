<?php

namespace App\Http\Controllers;

use App\Breakdown;
use App\BreakdownResource;
use App\BreakDownResourceShadow;
use App\Http\Requests\WipeRequest;
use App\Productivity;
use App\Project;
use App\Resources;
use App\WbsLevel;
use Barryvdh\Debugbar\Middleware\Debugbar;
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
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
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
        BreakDownResourceShadow::where('breakdown_resource_id', $breakdown_resource->id)->delete();
        $breakdown_resource->delete();

        $msg = 'Resource has been deleted';

        if ($request->ajax()) {
            return ['ok' => true, 'message' => $msg];
        }

        flash($msg, 'info');
        return \Redirect::to(route('project.show', $breakdown_resource->breakdown->project) . '#breakdown');
    }

    function wipe(WbsLevel $wbs_level, WipeRequest $request)
    {
        BreakdownResource::whereIn('breakdown_id', $wbs_level->breakdowns()->pluck('id'))->delete();
        BreakDownResourceShadow::whereIn('breakdown_id', $wbs_level->breakdowns()->pluck('id'))->delete();

        $msg = 'All breakdowns on this wbs-level have been removed';
        if ($request->ajax()) {
            return ['ok' => true, 'message' => $wbs_level];
        }

        flash($msg, 'info');
        return \Redirect::to(route('project.index') . '#breakdown');
    }

    function copy_wbs(WbsLevel $source_wbs, WbsLevel $target_wbs, Request $request)
    {
        $source_wbs->load(['breakdowns', 'breakdowns.resources']);
        foreach ($source_wbs->breakdowns as $breakdown) {
            $breakdownData = $breakdown->toArray();
            unset($breakdownData['id'], $breakdownData['wbs_level_id'], $breakdownData['created_at'], $breakdownData['updated_at']);

            /** @var Breakdown $newBreakdown */
            $newBreakdown = $target_wbs->breakdowns()->create($breakdownData);

            foreach ($breakdown->resources as $resource) {
                $resourceData = $resource->toArray();
                unset($resourceData['id'], $resourceData['breakdown_id'], $resourceData['created_at'], $resourceData['updated_at']);
                $newBreakdown->resources()->create($resourceData);
            }
        }

        if ($request->ajax()) {
            return ['ok' => true, 'message' => 'WBS data has been copied'];
        }

        return \Redirect::route('project.show', $source_wbs->project_id);
    }
}
