<?php

namespace App\Jobs;

use App\GlobalPeriod;
use App\Reports\Cost\GlobalReport;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;

class CacheGlobalReportJob extends Job implements ShouldQueue
{
    private $period;

    public function __construct(GlobalPeriod $period)
    {
        $this->period = $period;
    }

    public function handle()
    {
        $report = new GlobalReport($this->period);

        \Cache::put('global-report-' . $this->period->id, $report->run(), Carbon::parse('+2 days'));
    }
}
