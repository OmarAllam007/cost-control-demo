<?php

namespace App\Providers;

use App\Breakdown;
use App\BreakdownResource;
use App\BreakDownResourceShadow;
use App\BreakdownTemplate;
use App\BreakdownVariable;
use App\CostShadow;
use App\Observers\BreakdownObserver;
use App\Observers\BreakDownResourceObserver;
use App\Observers\BreakdownShadowObserver;
use App\Observers\BreakdownTemplateObserver;
use App\Observers\BreakdownVariableObserver;
use App\Observers\CostShadowObserver;
use App\Observers\GlobalReportObserver;
use App\Observers\ProductivityObserver;
use App\Observers\QuantitySurveyObserver;
use App\Observers\ResourcesObserver;
use App\Observers\ResourceTypeObserver;
use App\Observers\StdActivityObserver;
use App\Observers\WbsObserver;
use App\Productivity;
use App\Project;
use App\Resources;
use App\ResourceType;
use App\StdActivity;
use App\Survey;
use App\WbsLevel;
use Illuminate\Support\ServiceProvider;

class ObserversProvider extends ServiceProvider
{
    public function boot()
    {
        Project::observe(GlobalReportObserver::class);
        Productivity::observe(ProductivityObserver::class);
        BreakdownResource::observe(BreakDownResourceObserver::class);
        Resources::observe(ResourcesObserver::class);
        ResourceType::observe(ResourceTypeObserver::class);
        BreakdownTemplate::observe(BreakdownTemplateObserver::class);
        Breakdown::observe(BreakdownObserver::class);
        BreakDownResourceShadow::observe(BreakdownShadowObserver::class);
        Survey::observe(QuantitySurveyObserver::class);
        CostShadow::observe(CostShadowObserver::class);
        BreakdownVariable::observe(BreakdownVariableObserver::class);
        WbsLevel::observe(WbsObserver::class);
        StdActivity::observe(StdActivityObserver::class);
    }

    public function register()
    {
    }
}
