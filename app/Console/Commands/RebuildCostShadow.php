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

        $resources = WbsResource::joinShadow()->whereHas('period', function($q) {
            $q->where('is_open', true);
        })->get()->keyBy('breakdown_resource_id');

//        CostShadow::unguard();
        $count = $resources->count();
        $counter = 0;
        foreach ($resources as $resource) {
            $shadow = CostShadow::where([
                'breakdown_resource_id' => $resource->breakdown_resource_id,
                'period_id' => $resource->period_id
            ])->first();

            $result = $shadow->update($resource->toArray());
            if ($result) {
                $counter ++;
            }
        }

        $this->output->comment("$counter of $count records has been updated");

    }
}
