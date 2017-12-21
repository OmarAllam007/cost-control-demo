<?php

namespace App\Jobs;

use App\Jobs\Job;
use App\Reports\Cost\GlobalReport;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class CacheGlobalReportJob extends Job implements ShouldQueue
{
    public function handle()
    {
        $report = new GlobalReport();

        \Cache::put('global-report', $report->data(), Carbon::parse('+2 days'));
    }
}
