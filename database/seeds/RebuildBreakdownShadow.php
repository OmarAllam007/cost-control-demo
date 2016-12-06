<?php

use App\BreakdownResource;
use App\BreakDownResourceShadow;
use App\Formatters\BreakdownResourceFormatter;
use Illuminate\Database\Seeder;

class RebuildBreakdownShadow extends Seeder
{
    public function run()
    {
        BreakDownResourceShadow::truncate();

        set_time_limit(1800);

        try {
            $resources = $this->getUpdatedResources(BreakdownResource::all());

            foreach ($resources as $resource) {
                $formatter = new BreakdownResourceFormatter($resource);
                BreakDownResourceShadow::create($formatter->toArray());
            }
        } catch (\Exception $e) {
            echo $e->getTraceAsString();
        }
    }

    private function getUpdatedResources($breakdownResources)
    {
        \App\Resources::flushEventListeners();

        try {

            foreach ($breakdownResources as $breakdownResource) {
                $resource = $breakdownResource->resource;
                if (!$resource || !$resource->project_id) {
                    $resource_id = $breakdownResource->template_resource->resource_id;
                    $projectResource = \App\Resources::where('resource_id', $resource_id)->where('project_id', $breakdownResource->breakdown->project_id)->first();
                    if ($projectResource) {
                        $resource_id = $projectResource->id;
                    } else {
                        $resource = \App\Resources::withTrashed()->find($resource_id);
                        $resourceData = $resource->toArray();
                        unset($resourceData['id'], $resourceData['created_at'], $resourceData['updated_at']);
                        $resourceData['resource_id'] = $resource_id;
                        $resourceData['project_id'] = $breakdownResource->breakdown->project_id;
                        $projectResource = \App\Resources::create($resourceData);
                        $resource_id = $projectResource->id;
                    }
                }

                if (!empty($resource_id)) {


                    $breakdownResource->resource_id = $resource_id;
                    $breakdownResource->save();
                }
            }

            return $breakdownResources;
        } catch (\Exception $e) {
            echo $e->getTraceAsString();
        }


    }
}