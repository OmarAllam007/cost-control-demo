<?php

namespace App\Console\Commands;

use App\BreakdownResource;
use Illuminate\Console\Command;
use Symfony\Component\Console\Helper\ProgressBar;

class FixBudgetQty extends Command
{
    protected $signature = 'fix-budget-qty';
    protected $description = 'Fix budget qty';

    protected $counter = 0;

    /**
     * @var ProgressBar
     */
    protected $bar;

    public function handle()
    {
        $resources = BreakdownResource::where('budget_qty', 0)->orWhere('eng_qty', 0)->get();

        $this->bar = $this->output->createProgressBar($resources->count());

        $resources->each(function (BreakdownResource $resource) {
            $resource->budget_qty = $resource->breakdown->wbs_level->getBudgetQty($resource->breakdown->cost_account);
            $resource->eng_qty = $resource->breakdown->wbs_level->getEngQty($resource->breakdown->cost_account);

            $resource->save();
            $this->bar->advance();
        });

        $this->bar->finish();
    }
}
