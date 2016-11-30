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
    }

    public function register()
    {
        //
    }
}
