<?php

namespace App\Console\Commands;

use App\GlobalPeriod;
use App\Jobs\CacheGlobalReportJob;
use Illuminate\Console\Command;

class CacheGlobalReport extends Command
{
    protected $signature = 'global-report-cache';

    public function handle()
    {
        GlobalPeriod::latest()->take(12)->get()->each(function(GlobalPeriod $period) {
            dispatch(new CacheGlobalReportJob($period));
        });

    }
}
