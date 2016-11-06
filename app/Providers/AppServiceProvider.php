<?php

namespace App\Providers;

use App\CsiCategory;
use App\Jobs\CacheCsiCategoryTree;
use App\Jobs\CacheResourcesTree;
use App\Jobs\CacheWBSTree;
use App\Project;
use App\WbsLevel;
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
        \View::composer('project.tabs._boq', 'App\Http\ViewComposers\BoqComposer');

        \View::composer(['project.tabs._wbs','wbs-level._recursive_input','wbs-level._recursive','wbs-level._modal'], 'App\Http\ViewComposers\WbsComposer');
        \View::composer('csi-category.index', 'App\Http\ViewComposers\CsiCategoryComposer');

        $this->csiCategoryActions();
        $this->ResourceTypeActions();
        $this->wbsActions();
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function csiCategoryActions()
    {
        CsiCategory::saved(function () {
            \Cache::forget('csi-tree');
            dispatch(new CacheCsiCategoryTree());
        });

        CsiCategory::deleted(function () {
            \Cache::forget('csi-tree');
            dispatch(new CacheCsiCategoryTree());
        });

    }

    public function ResourceTypeActions()
    {
        CsiCategory::saved(function () {
            \Cache::forget('resources-tree');
            dispatch(new CacheResourcesTree());
        });
        CsiCategory::deleted(function () {
            \Cache::forget('resources-tree');
            dispatch(new CacheResourcesTree());
        });
    }

    public function wbsActions()
    {

        WbsLevel::saved(function (WbsLevel $wbs) {
            \Cache::forget('wbs-tree-' . $wbs->project_id);
            dispatch(new CacheWBSTree(Project::find($wbs->project_id)));

        });

        WbsLevel::deleted(function (WbsLevel $wbs) {
            \Cache::forget('wbs-tree-' . $wbs->project_id);
            dispatch(new CacheWBSTree(Project::find($wbs->project_id)));
        });
    }

    public function register()
    {
        //
    }
}
