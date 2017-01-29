<?php

namespace App\Observers;

use App\BreakdownResource;

use App\BreakDownResourceShadow;
use App\Formatters\BreakdownResourceFormatter;
use App\Resources;
use Illuminate\Database\Eloquent\Builder;
use Make\Makers\Resource;

class BreakDownResourceObserver
{
    function created(BreakdownResource $resource)
    {
        $formatter = new BreakdownResourceFormatter($resource);
        BreakDownResourceShadow::create($formatter->toArray());
    }

    function updated(BreakdownResource $resource)
    {
        $resource->updateShadow();
        $oldResource = Resources::find($resource->getOriginal('resource_id'));
        if ($oldResource) {
            $this->checkForResources($oldResource);
        }
    }

    function saving(BreakdownResource $breakdownResource)
    {
        if (!$breakdownResource->code) {
            $breakdownResource->code = $breakdownResource->breakdown->wbs_level->code . $breakdownResource->breakdown->std_activity->id_partial;
        }
        $breakdownResource->eng_qty = $breakdownResource->breakdown->qty_survey->eng_qty;
        $breakdownResource->budget_qty = $breakdownResource->breakdown->qty_survey->budget_qty;

        $resource_id = $breakdownResource->resource_id;
        if (!$resource_id) {
            $resource_id = $breakdownResource->template_resource->resource_id;
        }
        $resource = Resources::withTrashed()->find($resource_id);
        $project_id = $breakdownResource->breakdown->project_id;

        $projectResource = Resources::where(function (Builder $q) use ($resource, $resource_id) {
            $q->where('resource_id', $resource->id)->orWhere('id', $resource->id);
        })->whereProjectId($project_id)->first();

        if (!$projectResource) {
            $newResource = $resource->toArray();
            unset($newResource['id'], $newResource['created_at'], $newResource['updated_at']);
            $newResource['project_id'] = $project_id;
            $newResource['resource_id'] = $resource->id;
            Resources::flushEventListeners();
            $projectResource = Resources::create($newResource);
        }
        $breakdownResource->resource_id = $projectResource->id;
    }


    function deleting(BreakdownResource $resource)
    {
        BreakDownResourceShadow::where('breakdown_resource_id', $resource->id)->delete();
    }

    function deleted(BreakdownResource $resource)
    {
        $this->checkForResources($resource->resource);
    }

    function checkForResources($resource)
    {
        $breakdown_resource = BreakdownResource::where('resource_id', $resource->id)->exists();
        if ($resource->resource_id) {
            if (!$breakdown_resource) {
                Resources::where('id', $resource->id)->where('project_id', $resource->project_id)->forceDelete();
            }
        }
    }


}