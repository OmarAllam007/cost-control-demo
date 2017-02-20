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
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;


class CostController extends Controller
{
    function breakdowns(WbsLevel $wbs_level, Request $request)
    {
        set_time_limit(180);
        $period = $wbs_level->project->open_period();

        $perspective = $request->get('perspective');

        if ($perspective=='budget') {
            return BreakDownResourceShadow::joinCost($wbs_level, $period)->get();
        } else {
            return CostShadow::joinShadow($wbs_level, $period)->get();
        }
    }

    function activityLog(WbsLevel $wbs_level, Request $request)
    {
        $activities = collect();
        ActualResources::with('budget')->whereIn('wbs_level_id', $wbs_level->getChildrenIds())
            ->chunk(1000, function (Collection $resources) use ($activities) {
                foreach ($resources as $resource) {
                    $activity = $resource->budget->activity;

                    if (!$activities->has($activity)) {
                        $activities->put($activity, collect());
                    }

                    $activities->get($activity)->push($resource->toActivityLog());
                }
            });

        return $activities->map(function($resources) {
            return $resources->sortBy(function($resource) {
                return strtolower(trim($resource['store_resource_name']));
            });
        })->sortByKeys();
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

        ActualResources::whereIn('breakdown_resource_id', $resourceIds)
            ->where('period_id', $breakdown->project->open_period()->id)
            ->chunk(100, function (Collection $resources) {
                $resources->each(function (ActualResources $resource) {
                    $resource->delete();
                });
            });

        return ['ok' => true, 'message' => 'Activity data has been deleted'];
    }
}
