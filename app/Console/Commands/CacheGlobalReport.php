<?php

namespace App\Console\Commands;

use App\Jobs\CacheGlobalReportJob;
use Illuminate\Console\Command;

class CacheGlobalReport extends Command
{
    protected $signature = 'global-report-cache';

    public function handle()
    {
        dispatch(new CacheGlobalReportJob());
    }
}
