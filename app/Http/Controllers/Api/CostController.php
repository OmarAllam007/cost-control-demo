<?php

namespace App\Http\Controllers\Api;

use App\ActualBatch;
use App\ActualResources;
use App\Breakdown;
use App\BreakdownResource;
use App\BreakDownResourceShadow;
use App\CostIssue;
use App\CostResource;
use App\CostShadow;
use App\Http\Controllers\Controller;
use App\Import\ModifyBreakdown\Import;
use App\ImportantActualResource;
use App\Project;
use App\StoreResource;
use App\WbsLevel;
use App\WbsResource;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Fluent;


class CostController extends Controller
{
    function breakdowns(WbsLevel $wbs_level, Request $request)
    {
        $period = $wbs_level->project->open_period();

        $perspective = $request->get('perspective');

        $query = BreakDownResourceShadow::with('actual_resources')->whereIn('wbs_id', $wbs_level->getChildrenIds())->costOnly();
        if ($perspective != 'budget') {
            $query->whereRaw(
                "breakdown_resource_id in (select breakdown_resource_id from actual_resources where period_id = $period->id and deleted_at is null)"
            );
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

        $query->orderBy('activity')->orderBy('code');

        $page = request('page', 1);
        $perPage = 100;
        $total = $query->count();
        $last_page = ceil($total / $perPage);

        $rows = $query->forPage($page, $perPage)->get()
            ->reduce(function (\Illuminate\Support\Collection $collection, $resource) {
                $resource->appendFields();

                $code = $resource->code;
                $activity = $resource->activity;
                if (!$collection->has($code)) {
                    $collection->put($code, new Fluent([
                        'code' => $code,
                        'activity' => $activity,
                        'wbs_id' => $resource->wbs_id,
                        'resources' => collect()
                    ]));
                }

                $collection->get($code)->resources->push($resource);
                return $collection;
            }, collect());

        return ['total' => $total, 'current_page' => $page, 'perPage' => $perPage, 'last_page' => $last_page, 'data' => $rows];
    }

    function activityLog(WbsLevel $wbs_level, Request $request)
    {
        $activities = collect();
        ActualResources::with('budget')->whereIn('wbs_level_id', $wbs_level->getChildrenIds())
            ->chunk(1000, function (Collection $resources) use ($activities) {
                foreach ($resources as $resource) {
                    $activity = $resource->budget->activity ?? null;
                    if (!$activity) continue;

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
            ->where('resource_id', '!=', 0)
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
        if (cannot('actual_resources', $breakdown_resource->project)) {
            return ['ok' => false, 'message' => 'You are not authorized to do this action'];
        }

        $period_id = $breakdown_resource->project->open_period()->id;
        ActualResources::where('breakdown_resource_id', $breakdown_resource->id)
            ->where('period_id', $period_id)
            ->get()->map(function (ActualResources $resource) {
                return $resource->forceDelete();
            })->filter()->count();

        ImportantActualResource::where('period_id', $period_id)
            ->whereRaw(
                "(breakdown_resource_id = {$breakdown_resource->id} or " .
                "breakdown_resource_id in (select id from breakdown_resources where " .
                "rollup_resource_id = {$breakdown_resource->id}))"
            )->delete();
        return ['ok' => true, 'message' => 'Resource data has been deleted'];
    }

    function deleteBatch(ActualBatch $actual_batch)
    {
        if (cannot('actual_resources', $actual_batch->project)) {
            return ['ok' => false, 'message' => 'You are not authorized to do this action'];
        }

        set_time_limit(600);

        ActualResources::where('batch_id', $actual_batch->id)
            ->get()->each(function (ActualResources $resource) {
                $resource->forceDelete();
            });

        StoreResource::where('batch_id', $actual_batch->id)->delete();
        ImportantActualResource::where('batch_id', $actual_batch->id)->delete();
        CostIssue::where('batch_id', $actual_batch->id)->delete();

        $actual_batch->delete();

        return ['ok' => true, 'message' => 'Resources data has been deleted'];
    }

    function deleteActivity(WbsLevel $wbs)
    {
        if (cannot('actual_resources', $wbs->project)) {
            return ['ok' => false, 'message' => 'You are not authorized to do this action'];
        }

        $code = request('code');

        $resourceIds = $wbs->project->shadows()
            ->whereIn('wbs_id', $wbs->getChildrenIds())
            ->where('code', $code)->pluck('breakdown_resource_id');

        ActualResources::whereIn('breakdown_resource_id', $resourceIds)
            ->where('period_id', $wbs->project->open_period()->id)
            ->chunkById(1000, function (Collection $resources) {
                $resources->each(function (ActualResources $resource) {
                    $resource->forceDelete();
                });
            });

        ImportantActualResource::query()
            ->whereIn('breakdown_resource_id', $resourceIds)
            ->where('period_id', $wbs->project->open_period()->id)->delete();

        return ['ok' => true, 'message' => 'Activity data has been deleted'];
    }

    function deleteWbs(WbsLevel $wbs_level)
    {
        if (cannot('cost_owner', $wbs_level->project)) {
            return ['ok' => false, 'message' => 'You are not authorized to do this action'];
        }

        $breakdown_resource_ids = BreakdownResource::whereIn('wbs_id', $wbs_level->getChildrenIds())->pluck('id');

        $period_id = $wbs_level->project->open_period()->id;
        ActualResources::whereIn('breakdown_resource_id', $wbs_level->getChildrenIds())
            ->where('period_id', $period_id)
            ->chunkById(1000, function (Collection $resources) {
                $resources->each(function (ActualResources $resource) {
                    $resource->forceDelete();
                });
            });

        ImportantActualResource::query()
            ->whereIn('breakdown_resource_id', $breakdown_resource_ids)
            ->where('period_id', $period_id)->delete();

        return ['ok' => true, 'message' => 'WBS current data has been deleted'];
    }

    function deleteProject(Project $project)
    {
        if (cannot('cost_owner', $project)) {
            return ['ok' => false, 'message' => 'You are not authorized to do this action'];
        }

        $period_id = $project->open_period()->id;
        ActualResources::where('project_id', $project->id)
            ->where('period_id', $period_id)
            ->chunkById(1000, function (Collection $resources) {
                $resources->each(function (ActualResources $resource) {
                    $resource->forceDelete();
                });
            });

        ImportantActualResource::query()
            ->where('project_id', $project->id)
            ->where('period_id', $period_id)->delete();

        return ['ok' => true, 'message' => 'Project current data has been deleted'];
    }
}
