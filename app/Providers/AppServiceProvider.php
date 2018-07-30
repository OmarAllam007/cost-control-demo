<?php

namespace App\Providers;

use App\Support\ChangeLogger;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if (config('app.debug_bar')) {
            app()->register(\Barryvdh\Debugbar\ServiceProvider::class);
        }

        if (PHP_SAPI == 'cli') {
            app()->instance('change_log', new ChangeLogger());
        }
    }

    public function register()
    {
        //
    }
}
