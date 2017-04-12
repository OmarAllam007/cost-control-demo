<?php

namespace App\Providers;

use App\Breakdown;
use App\BreakdownResource;
use App\BreakDownResourceShadow;
use App\BreakdownTemplate;
use App\BreakdownVariable;
use App\CostShadow;
use App\CsiCategory;
use App\Jobs\CacheCsiCategoryTree;
use App\Jobs\CacheResourcesTree;
use App\Jobs\CacheWBSTree;
use App\Observers\BreakdownObserver;
use App\Observers\BreakDownResourceObserver;
use App\Observers\BreakdownShadowObserver;
use App\Observers\BreakdownTemplateObserver;
use App\Observers\BreakdownVariableObserver;
use App\Observers\BreakdownVariablesObserver;
use App\Observers\CostShadowObserver;
use App\Observers\ProductivityObserver;
use App\Observers\QSObserver;
use App\Observers\QuantitySurveyObserver;
use App\Observers\ResourcesObserver;
use App\Observers\StandardActivityResourceObserver;
use App\Observers\WbsObserver;
use App\Productivity;
use App\Project;
use App\Resources;
use App\StdActivityResource;
use App\Support\ChangeLogger;
use App\Survey;
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
        \View::composer('resource-type._modal', 'App\Http\ViewComposers\ResourcesComposer');
        \View::composer('resource-type._modal2', 'App\Http\ViewComposers\ResourcesComposer');

        \View::composer(['project.show', 'project.tabs._wbs','wbs-level._recursive_input','wbs-level._recursive','wbs-level._modal','wbs-level.report', 'project.cost-control.wbs'], 'App\Http\ViewComposers\WbsComposer');
        \View::composer('csi-category.index', 'App\Http\ViewComposers\CsiCategoryComposer');

        $this->csiCategoryActions();
        $this->ProductivityActions();
        $this->ResourceTypeActions();
        $this->wbsActions();

        Productivity::observe(ProductivityObserver::class);
        BreakdownResource::observe(BreakDownResourceObserver::class);
        Resources::observe(ResourcesObserver::class);
        BreakdownTemplate::observe(BreakdownTemplateObserver::class);
        Breakdown::observe(BreakdownObserver::class);
        BreakDownResourceShadow::observe(BreakdownShadowObserver::class);
        Survey::observe(QuantitySurveyObserver::class);
        StdActivityResource::observe(StandardActivityResourceObserver::class);
        Survey::observe(QSObserver::class);
        CostShadow::observe(CostShadowObserver::class);
//        BreakdownVariable::observe(BreakdownVariableObserver::class);
        BreakdownVariable::observe(BreakdownVariableObserver::class);
        WbsLevel::observe(WbsObserver::class);

        if (PHP_SAPI == 'cli') {
            app()->instance('change_log', new ChangeLogger());
        }
    }



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
    public function ProductivityActions()
    {
        Productivity::saved(function () {
            \Cache::forget('csi-tree');
            dispatch(new CacheCsiCategoryTree());
        });

        Productivity::deleted(function () {
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
