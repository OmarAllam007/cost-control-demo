<?php

namespace App\Http\Controllers;

use App\Breakdown;
use App\BreakdownResource;
use App\BreakDownResourceShadow;
use App\Http\Requests\WipeRequest;
use App\Observers\BreakDownResourceObserver;
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
        $breakdown_resource->breakdown->fill($request->only(['std_activity_id', 'cost_account', 'wbs_level_id']));
        $breakdown_resource->breakdown->save();

        $breakdown_resource->fill($request->only('labor_count', 'productivity_id', 'resource_id', 'equation'));
        $breakdown_resource->save();

        $breakdown_resource->breakdown->resources->each(function(BreakdownResource $resource){
            $resource->updateShadow();
        });

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
        $breakdown_ids = Breakdown::where('wbs_level_id', $wbs_level->id)->pluck('id');
        $breakdown_resource_ids = BreakdownResource::whereIn('breakdown_id', $breakdown_ids)->pluck('id');

        BreakDownResourceShadow::whereIn('breakdown_resource_id', $breakdown_resource_ids)->delete();
        BreakdownResource::where('id', $breakdown_resource_ids)->delete();
        Breakdown::whereIn('id', $breakdown_ids)->delete();

        $msg = 'All breakdowns on this wbs-level have been removed';
        if ($request->ajax()) {
            return ['ok' => true, 'message' => $msg];
        }

        flash($msg, 'info');
        return \Redirect::to(route('project.index') . '#breakdown');
    }

    function copy_wbs(WbsLevel $source_wbs, WbsLevel $target_wbs, Request $request)
    {
        set_time_limit(600);

        $source_wbs->load(['breakdowns', 'breakdowns.resources']);

        foreach ($source_wbs->breakdowns as $breakdown) {
            $breakdownData = $breakdown->getAttributes();
            unset($breakdownData['id'], $breakdownData['created_at'], $breakdownData['updated_at']);
            $breakdownData['wbs_level_id'] = $target_wbs->id;
            $newBreakdown = Breakdown::create($breakdownData);

            $variables = $breakdown->variables->pluck('value', 'display_order');
            $newBreakdown->syncVariables($variables);

            foreach ($breakdown->resources as $resource) {
                $resourceData = $resource->getAttributes();
                if ($newBreakdown->qty_survey) {
                    $resourceData['budget_qty'] = $newBreakdown->qty_survey->budget_qty;
                    $resourceData['eng_qty'] = $newBreakdown->qty_survey->eng_qty;
                } else {
                    $resourceData['budget_qty'] = 0;
                    $resourceData['eng_qty'] = 0;
                }
                unset($resourceData['id'], $resourceData['breakdown_id'], $resourceData['created_at'], $resourceData['updated_at']);
                $newBreakdown->resources()->create($resourceData);
            }
        }

        if ($request->ajax()) {
            $breakdowns = BreakDownResourceShadow::where('wbs_id', $target_wbs->id)->get();
            return ['ok' => true, 'breakdowns' => $breakdowns];
        }

        return \Redirect::route('project.show', $source_wbs->project_id);
    }

    function deleteAllBreakdowns(Project $project)
    {

        BreakdownResource::whereIn('breakdown_id', $project->breakdowns()->pluck('id'))->delete();
        BreakDownResourceShadow::where('project_id', $project->id)->delete();
        return redirect()->back();
    }
}
