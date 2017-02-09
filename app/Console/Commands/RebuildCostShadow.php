<?php

namespace App\Console\Commands;

use App\BreakdownResource;
use App\BreakDownResourceShadow;
use App\CostShadow;
use App\WbsResource;
use Illuminate\Console\Command;

class RebuildCostShadow extends Command
{
    protected $signature = 'cost:rebuild-shadow';

    protected $description = 'Rebuild shadow for cost data';

    public function handle()
    {
        BreakDownResourceShadow::where('status', 'Closed')->update(['progress' => 100]);

        $resources = WbsResource::joinShadow()->get();

//        CostShadow::unguard();
        $count = $resources->count();
        $counter = 0;
        CostShadow::truncate();
        $shadows = collec();
        foreach ($resources as $resource) {
            $shadows->push($resource->toArray());
        }

        $this->output->comment("$counter of $count records has been updated");

    }
}
