<?php

namespace App\Http\Controllers\Api;

use App\ActualResources;
use App\Breakdown;
use App\BreakdownResource;
use App\BreakDownResourceShadow;
use App\CostResource;
use App\CostShadow;
use App\Http\Controllers\Controller;
use App\Project;
use App\WbsLevel;
use App\WbsResource;
use Illuminate\Http\Request;


class CostController extends Controller
{
    function breakdowns(WbsLevel $wbs_level, Request $request)
    {
//        return WbsResource::whereIn('wbs_level_id', $wbs_level->getChildrenIds())->where('period_id', $wbs_level->project->open_period()->id)
//            ->joinShadow()
//            ->get();

        $period = $wbs_level->project->open_period();
        if (!$period) {
            return [];
        }

        if ($request->has('all')) {
            return BreakDownResourceShadow::where('wbs_id', $wbs_level->getChildrenIds())->with('cost')->get();
        }

        return CostShadow::whereIn('wbs_level_id', $wbs_level->getChildrenIds())
            ->where('period_id', $period->id)->with('budget')->get();
    }

    function resources(Project $project)
    {
        $period = $project->open_period();

        if (!$period) {
            return [];
        }

        return CostResource::where('project_id', $project->id)
            ->where('period_id', $period->id)
            ->get()->map(function (CostResource $resource) {
                return $resource->jsonFormat();
            });
    }

    function deleteResource(BreakdownResource $breakdown_resource)
    {
        if (cannot('actual_resources', $breakdown_resource->breakdown->project)) {
            return ['ok' => false, 'message' => 'You are not authorized to do this action'];
        }

        $counter = ActualResources::where('breakdown_resource_id', $breakdown_resource->id)
            ->where('period_id', $breakdown_resource->breakdown->project->open_period()->id)
            ->get()->map(function (ActualResources $resource) {
                return $resource->delete();
            })->filter()->count();

        if ($counter) {
            return ['ok' => true, 'message' => 'Resource data has been deleted'];
        } else {
            return ['ok' => false, 'message' => 'Could not delete resource'];
        }
    }

    function deleteActivity(Breakdown $breakdown)
    {
        if (cannot('actual_resources', $breakdown->project)) {
            return ['ok' => false, 'message' => 'You are not authorized to do this action'];
        }

        $resourceIds = $breakdown->resources->pluck('id');

        $resources = ActualResources::whereIn('breakdown_resource_id', $resourceIds)
            ->where('period_id', $breakdown->project->open_period()->id)
            ->get()->each(function (ActualResources $resource) {
                $resource->delete();
            });

        return ['ok' => true, 'message' => 'Activity data has been deleted'];
    }
}
