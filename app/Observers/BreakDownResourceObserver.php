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

    function updated(BreakdownResource $resource)
    {
        $formatter = new BreakdownResourceFormatter($resource);
        $shadow = BreakDownResourceShadow::where('breakdown_resource_id', $resource->id)->update($formatter->toArray());
    }

    function saving(BreakdownResource $breakdownResource)
    {
        $resource = Resources::find($breakdownResource->template_resource->resource_id);
        if (!$resource->project_id) {
            $projectResource = Resources::whereResourceId($resource->id)->whereProjectId($resource->project_id)->first();
            if (!$projectResource) {
                $newResource = $resource->toArray();
                unset($newResource['id'], $newResource['created_at'], $newResource['updated_at']);
                $newResource['project_id'] = $resource->project_id;
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