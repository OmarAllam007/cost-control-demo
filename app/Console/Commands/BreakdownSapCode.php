<?php

namespace App\Console\Commands;

use App\Breakdown;
use App\BreakdownResource;
use App\BreakDownResourceShadow;
use Illuminate\Console\Command;
use Symfony\Component\Console\Helper\ProgressBar;

class BreakdownSapCode extends Command
{
    protected $signature = 'sap-code:breakdown';

    protected $description = 'Generate SAP code for breakdowns and breakdown resources';
    /** @var ProgressBar */
    private $bar;

    public function __construct()
    {
        parent::__construct();
        Breakdown::flushEventListeners();
        BreakdownResource::flushEventListeners();
        BreakDownResourceShadow::flushEventListeners();
    }

    public function handle()
    {
        $this->bar = $this->output->createProgressBar(Breakdown::count());
        $this->bar->setBarWidth(60);

        Breakdown::with(['wbs_level', 'std_activity'])->chunk(5000, function($breakdowns) {
            \DB::transaction(function () use ($breakdowns){
                $breakdowns->each(function(Breakdown $breakdown) {
                    $breakdown->sap_code = $breakdown->wbs_level->sap_code . '.' . $breakdown->std_activity->sap_code_partial;
                    $breakdown->save();

                    $breakdown->resources()->update(['sap_code' => $breakdown->sap_code]);
                    BreakDownResourceShadow::where('breakdown_id', $breakdown->id)->update(['sap_code' => $breakdown->sap_code]);
                    $this->bar->advance();
                });
            });
        });

        $this->bar->finish();
    }
}
