<?php

namespace App\Http\Controllers\Api;

use App\ActualBatch;
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
$time = microtime(1);
        $period = $wbs_level->project->open_period();

        $perspective = $request->get('perspective');

        if ($perspective == 'budget') {
            $query = BreakDownResourceShadow::joinCost($wbs_level, $period)->with('actual_resources');
        } else {
            $query = CostShadow::joinShadow($wbs_level, $period);
        }

        if ($activity_id = $request->get('activity')) {
            $query->where(compact('activity_id'));
        }

        if ($resource_type_id = $request->get('resource_type')) {
            $query->where(compact('resource_type_id'));
        }

        if ($cost_account = $request->get('cost_account')) {
            $query->where('cost_account', 'like', "%{$cost_account}%");
        }

        if ($resource = $request->get('resource')) {
            $query->where(function ($q) use ($resource) {
                $q->where('resource_code', 'like', "%{$resource}%")->orWhere('resource_name', 'like', "%{$resource}%");
            });
        }

        $rows = $query->paginate(100);
        if ($perspective == 'budget') {
            $rows->each(function (BreakDownResourceShadow $resource) {
                $resource->appendFields();
            });
        }

        return $rows;
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

        return $activities->map(function ($resources) {
            return $resources->sortBy(function ($resource) {
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

    function batches(Project $project)
    {
        if (!can('actual_resources', $project)) {
            return ['ok' => false, 'message' => 'Your are not authorized to do this action'];
        }

        $query = ActualBatch::whereProjectId($project->id)->latest();
        if (!can('cost_owner', $project)) {
            $query->whereUserId(auth()->id());
        }

        return ['ok' => true, 'batches' => $query->get()];
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

    function deleteBatch(ActualBatch $actual_batch)
    {
        if (cannot('actual_resources', $actual_batch->project)) {
            return ['ok' => false, 'message' => 'You are not authorized to do this action'];
        }

        set_time_limit(600);

        ActualResources::where('batch_id', $actual_batch->id)
            ->get()->each(function (ActualResources $resource) {
                $resource->delete();
            });

        $actual_batch->delete();

        return ['ok' => true, 'message' => 'Resources data has been deleted'];
    }

    function deleteActivity(Breakdown $breakdown)
    {
        if (cannot('actual_resources', $breakdown->project)) {
            return ['ok' => false, 'message' => 'You are not authorized to do this action'];
        }

        $resourceIds = $breakdown->resources->pluck('id');

        ActualResources::whereIn('breakdown_resource_id', $resourceIds)
            ->where('period_id', $breakdown->project->open_period()->id)
            ->chunkById(1000, function (Collection $resources) {
                $resources->each(function (ActualResources $resource) {
                    $resource->delete();
                });
            });

        return ['ok' => true, 'message' => 'Activity data has been deleted'];
    }

    function deleteWbs(WbsLevel $wbs_level)
    {
        if (cannot('cost_owner', $wbs_level->project)) {
            return ['ok' => false, 'message' => 'You are not authorized to do this action'];
        }

        ActualResources::whereIn('wbs_level_id', $wbs_level->getChildrenIds())
            ->where('period_id', $wbs_level->project->open_period()->id)
            ->chunkById(1000, function (Collection $resources) {
                $resources->each(function (ActualResources $resource) {
                    $resource->delete();
                });
            });

        return ['ok' => true, 'message' => 'WBS current data has been deleted'];
    }

    function deleteProject(Project $project)
    {
        if (cannot('cost_owner', $project)) {
            return ['ok' => false, 'message' => 'You are not authorized to do this action'];
        }

        ActualResources::whereIn('wbs_level_id', $project->id)
            ->where('period_id', $project->open_period()->id)
            ->chunkById(1000, function (Collection $resources) {
                $resources->each(function (ActualResources $resource) {
                    $resource->delete();
                });
            });

        return ['ok' => true, 'message' => 'Project current data has been deleted'];
    }
}
