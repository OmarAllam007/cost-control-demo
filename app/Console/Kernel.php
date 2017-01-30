<?php

namespace App\Console;

use App\Console\Commands\CleanResourceTypes;
use App\Console\Commands\CostUpdateReminder;
use App\Console\Commands\FixBudgetQty;
use App\Console\Commands\RebuildCostShadow;
use App\Console\Commands\RebuildBudgetShadow;
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
