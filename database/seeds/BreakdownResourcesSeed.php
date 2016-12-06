<?php

use Illuminate\Database\Seeder;

class BreakdownResourcesSeed extends Seeder
{

    public function run()
    {
        $resources = \App\BreakdownResource::all();
        foreach ($resources as $resource){
            $resource->update(['resource_id'=>$resource->std_activity_resource_id]);
        }
    }
}
