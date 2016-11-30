<?php

namespace App\Providers;

use App\Breakdown;
use App\BreakdownResource;
use App\BreakDownResourceShadow;
use App\Formatters\BreakdownResourceFormatter;
//use App\Resources;
use App\Resources;
use Illuminate\Support\ServiceProvider;

class BreakdownResourcesShadowProvider extends ServiceProvider
{

    public function boot()
    {

        BreakdownResource::creating(function (BreakdownResource $resource) {
            $resource->resource_id = $resource->template_resource->resource->id;
            $resource->update();
        });

        Breakdown::updated(function (Breakdown $breakdown) {
            $shadows = BreakDownResourceShadow::where('breakdown_id', $breakdown->id)->get();
            /** @var BreakDownResourceShadow $shadow */
            foreach ($shadows as $shadow) {
                $shadow->wbs_id = $breakdown->wbs_level_id;
                $shadow->update();
            }
        });
    }

    public function register()
    {
        //
    }
}
