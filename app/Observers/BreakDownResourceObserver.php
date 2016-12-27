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
        $shadow = BreakDownResourceShadow::create($formatter->toArray());
    }

    function creating(BreakdownResource $resource)
    {

        $resource->code = $resource->breakdown->wbs_level->code . $resource->breakdown->std_activity->id_partial;
        $resource->eng_qty = $resource->breakdown->wbs_level->getEngQty($resource->breakdown->cost_account);
        $resource->budget_qty = $resource->breakdown->wbs_level->getBudgetQty($resource->breakdown->cost_account);
    }

    function updated(BreakdownResource $resource)
    {

        $resource->updateShadow();

        $oldResource = Resources::find($resource->getOriginal('resource_id'));
        $this->checkForResources($oldResource);
    }

    function saving(BreakdownResource $breakdownResource)
    {
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
        $breakdown_resource = BreakdownResource::where('resource_id', $resource->id)->first();
        if (!$breakdown_resource) {
            Resources::where('id', $resource->id)->where('project_id', $resource->project_id)->forceDelete();
        }

    }


}