<?php

namespace App\Console;

use App\Console\Commands\AddBoqAndSurveyToBreakdownShadow;
use App\Console\Commands\AddBoqAndSurveyToBreakdownShadowProject;
use App\Console\Commands\AddBoqDisciplineToMasterShadow;
use App\Console\Commands\AddBoqToMasterSahdow;
use App\Console\Commands\CleanResourceTypes;
use App\Console\Commands\CostUpdateReminder;
use App\Console\Commands\CreateRevisions;
use App\Console\Commands\CreateRevisionsForCurrentProject;
use App\Console\Commands\ExportAllResources;
use App\Console\Commands\FixBreakdownVars;
use App\Console\Commands\FixBudgetQty;
use App\Console\Commands\FixModResources;
use App\Console\Commands\FixProductivity;
use App\Console\Commands\FixProjectProductivity;
use App\Console\Commands\RebuildCostShadow;
use App\Console\Commands\RebuildBudgetShadow;
use App\Console\Commands\RecalculateCost;
use App\Console\Commands\UpdateActivityCode;
use App\Console\Commands\UpdateResourceTypesAndResources;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        Commands\CacheGlobalReport::class,
        Commands\WbsSapCode::class,
    ];

    protected function schedule(Schedule $schedule)
    {
        $schedule->command('global-report-cache')->dailyAt('02:00');
    }
}
