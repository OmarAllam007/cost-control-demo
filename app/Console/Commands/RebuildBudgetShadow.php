<?php

namespace App\Console\Commands;

use App\BreakdownResource;
use App\BreakDownResourceShadow;
use App\WbsLevel;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Symfony\Component\Console\Helper\ProgressBar;

class RebuildBudgetShadow extends Command
{
    protected $signature = 'rebuild-budget-shadow';

    protected $description = 'Rebuild shadow table for budget breakdown resources';

    /** @var ProgressBar */
    protected $bar;

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $count = BreakDownResourceShadow::count();
        $this->output->comment($count . ' Resources found');
        $this->bar = $this->output->createProgressBar($count);

        BreakDownResourceShadow::with('breakdown_resource')->chunk(25000, function(Collection $shadows) {
            $shadows->pluck('breakdown_resource')->each(function(BreakdownResource $resource) {
                $resource->updateShadow();
                $this->bar->advance();
            });
        });

        $this->bar->finish();
    }
}
