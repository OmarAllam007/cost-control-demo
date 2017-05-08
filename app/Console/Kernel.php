<?php

namespace App\Console;

use App\Console\Commands\AddBoqAndSurveyToBreakdownShadow;
use App\Console\Commands\AddBoqAndSurveyToBreakdownShadowProject;
use App\Console\Commands\AddBoqDisciplineToMasterShadow;
use App\Console\Commands\AddBoqToMasterSahdow;
use App\Console\Commands\CleanResourceTypes;
use App\Console\Commands\CostUpdateReminder;
use App\Console\Commands\ExportAllResources;
use App\Console\Commands\FixBreakdownVars;
use App\Console\Commands\FixBudgetQty;
use App\Console\Commands\FixModResources;
use App\Console\Commands\FixProductivity;
use App\Console\Commands\RebuildCostShadow;
use App\Console\Commands\RebuildBudgetShadow;
use App\Console\Commands\RecalculateCost;
use App\Console\Commands\UpdateActivityCode;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        CostUpdateReminder::class,
        CleanResourceTypes::class,
        FixBudgetQty::class,
        RebuildCostShadow::class,
        RebuildBudgetShadow::class,
        UpdateActivityCode::class,
        FixBreakdownVars::class,
        ExportAllResources::class,
        RecalculateCost::class,
        FixModResources::class,
        AddBoqDisciplineToMasterShadow::class,
        AddBoqToMasterSahdow::class,
        AddBoqAndSurveyToBreakdownShadow::class,
        AddBoqAndSurveyToBreakdownShadowProject::class,
        FixProductivity::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();
    }
}
