<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Make\Makers\Resource;

class IdResourceGeneratorServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        Resource::creating(function (Resource $resource){
            return $resource;
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
