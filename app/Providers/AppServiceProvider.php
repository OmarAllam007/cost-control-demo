<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        \View::composer('std-activity-resource._resources_modal', 'App\Http\ViewComposers\ResourcesComposer');
        \View::composer('project.tabs._wbs', 'App\Http\ViewComposers\WbsComposer');
        \View::composer('wbs-level._modal', 'App\Http\ViewComposers\WbsComposer');
        \View::composer('csi-category.index', 'App\Http\ViewComposers\CsiCategoryComposer');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
