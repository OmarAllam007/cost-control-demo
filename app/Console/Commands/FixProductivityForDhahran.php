<?php

namespace App\Console\Commands;

use App\BreakDownResourceShadow;
use App\Productivity;
use Illuminate\Console\Command;

class FixProductivityForDhahran extends Command
{
    protected $signature = 'fix-dhahran-productivity';

    public function handle()
    {
        BreakDownResourceShadow::flushEventListeners();
        Productivity::flushEventListeners();

        Productivity::whereProjectId(61)->get()->each(function ($prod) {
            $attr = $prod->toArray();
            unset($attr['id']);
            $attr['project_id'] = 90;

            $newProductivity = Productivity::create($attr);
            BreakDownResourceShadow::whereProjectId(90)
                ->whereIn('productivity_id', [$prod->id, $prod->productivity_id])
                ->update([
                    'productivity_id' => $newProductivity->id,
                    'productivity_output' => $newProductivity->after_reduction
                ]);
        });
    }
}
