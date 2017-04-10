<?php

namespace App\Console\Commands;

use App\CostShadow;
use App\Support\CostShadowCalculator;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Symfony\Component\Console\Helper\ProgressBar;

class RecalculateCost extends Command
{
    protected $signature = 'cost:recalculate';
    protected $description = 'Recalculate cost fields';

    protected $fields = [
        // 'pw_index', 'allowable_ev_cost', 'allowable_var', 'bl_allowable_cost', 'bl_allowable_var', 'remaining_qty', 'remaining_cost', 'remaining_unit_price'
    ];
    /** @var ProgressBar */
    protected $bar;

    public function handle()
    {
        $this->bar = $this->output->createProgressBar(CostShadow::wherePeriodId(8)->count());
        $this->bar->setBarWidth(50);

        CostShadow::wherePeriodId(8)->chunk(1000, function (Collection $resources) {
            $resources->each(function(CostShadow $resource) {
                $calc = new CostShadowCalculator($resource);
                $attributes = collect($calc->toArray());

                if ($this->fields) {
                    $attributes = $attributes->only($this->fields);
                }

                $resource->update($attributes->toArray());
                $this->bar->advance();
            });
        });

        $this->bar->finish();
    }
}
