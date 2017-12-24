<?php

namespace App\Jobs;

use App\Reports\Cost\GlobalReport;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;

class CacheGlobalReportJob extends Job implements ShouldQueue
{
    public function handle()
    {
        $report = new GlobalReport();

        \Cache::put('global-report', $report->run(), Carbon::parse('+2 days'));
    }
}
