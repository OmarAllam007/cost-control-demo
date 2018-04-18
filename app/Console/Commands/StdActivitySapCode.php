<?php

namespace App\Console\Commands;

use App\StdActivity;
use Illuminate\Console\Command;

class StdActivitySapCode extends Command
{
    protected $signature = 'activities:sap-code';

    protected $description = 'Generate SAP code partial';

    public function __construct()
    {
        parent::__construct();
        StdActivity::flushEventListeners();
    }

    public function handle()
    {
        StdActivity::each(function($activity) {
            $part1 = substr($activity->id_partial, 0, 2);
            $part2 = substr($activity->id_partial, -2);

            $activity->sap_code_partial = $part1 . '.' . $part2;
            $activity->save();
        });
    }
}
