<?php

namespace App\Providers;

use App\Breakdown;
use App\BreakdownResource;
use App\BreakDownResourceShadow;
use App\Formatters\BreakdownResourceFormatter;
use App\Resources;
use Illuminate\Support\ServiceProvider;

class BreakdownResourcesShadowProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        BreakdownResource::creating(function (BreakdownResource $resource) {
            $resource->resource_id = $resource->template_resource->resource->id;
            $resource->update();
        });


        Resources::updated(function (Resources $resource) {
            $breakdown_resources = BreakdownResource::where('resource_id', $resource->id)->get();
            foreach ($breakdown_resources as $breakdown_resource) {
                $formatter = new BreakdownResourceFormatter($breakdown_resource);
                BreakDownResourceShadow::where('breakdown_resource_id', $breakdown_resource->id)->update($formatter->toArray());
            }
        });

    }

    public function register()
    {
        //
    }
}
