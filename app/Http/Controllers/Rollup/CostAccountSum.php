<?php

namespace App\Http\Controllers\Rollup;


use App\Breakdown;
use App\BreakdownResource;
use App\Http\Controllers\Controller;
use App\Project;
use App\WbsLevel;
use Illuminate\Http\Request;

class CostAccountSum extends Controller
{
    /*function create(Project $project, BreakdownResource $resource)
    {
        $this->authorize('cost_owner', $project);

        $similar_resources = BreakdownResource::where('breakdown_id', $resource->breakdown_id)
            ->where('resources_id', $resource->resource_id)->get();

        if ($similar_resources->count() < 2) {
            flash('Not enough resources to be summed', 'warning');
            $url = request()->exists('iframe')? '/blank' : route('project.cost-control', $project);
            return redirect($url);
        }

        return view('rollup.sum.cost_account');
    }*/

    function store(WbsLevel $wbs)
    {
        $this->authorize('cost_owner', $wbs->project);

        $breakdowns = Breakdown::whereIn('wbs_id', $wbs->getChildrenIds())->get()->each(function() {

        });

        return redirect()->route('project.cost-control', $wbs->project);
    }
}