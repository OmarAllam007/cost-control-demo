<?php

namespace App\Observers;

use App\BreakdownResource;

use App\BreakDownResourceShadow;
use App\Formatters\BreakdownResourceFormatter;
use App\Resources;
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
    }

    function updated(BreakdownResource $resource)
    {
        $formatter = new BreakdownResourceFormatter($resource);
        $shadow = BreakDownResourceShadow::firstOrCreate(['breakdown_resource_id' => $resource->id]);
        $shadow->update($formatter->toArray());
    }

    function saving(BreakdownResource $breakdownResource)
    {
        $resource = Resources::withTrashed()->find($breakdownResource->template_resource->resource_id);
        if (!$resource->project_id) {
            $project_id = $breakdownResource->breakdown->project_id;
            $projectResource = Resources::whereResourceId($resource->id)->whereProjectId($project_id)->first();
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
    }

    function deleted(BreakdownResource $resource)
    {
        BreakDownResourceShadow::where('breakdown_resource_id', $resource->id)->delete();
    }




}