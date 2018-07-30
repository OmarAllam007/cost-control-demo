<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ViewComposersProvider extends ServiceProvider
{
    public function boot()
    {
        \View::composer([
            'resource-type._modal2', 'resource-type._modal', 'std-activity-resource._resources_modal', 'resources._form',
            'resources._filters'
        ], 'App\Http\ViewComposers\ResourcesComposer');

        \View::composer('project.tabs._boq', 'App\Http\ViewComposers\BoqComposer');

        \View::composer([
            'project.show', 'project.tabs._wbs','wbs-level._modal',
            'wbs-level.report', 'project.cost-control.wbs',
            'rollup.cost-account', 'rollup.semi-cost-account', 'rollup.semi-activity',
        ], 'App\Http\ViewComposers\WbsComposer');

        \View::composer('csi-category.index', 'App\Http\ViewComposers\CsiCategoryComposer');
    }

    public function register()
    {
        //
    }
}
