<?php

namespace App\Console\Commands;

use App\BreakDownResourceShadow;
use App\Productivity;
use Illuminate\Console\Command;

class FixProductivity extends Command
{
    protected $signature = 'fix-productivity';

    public function handle()
    {
//        $productivities = Productivity::whereProjectId(35)->get();

//        $bar = $this->output->createProgressBar($productivities->count());
//
//        foreach ($productivities as $productivity) {
//            dump($productivity->csi_code);
//            BreakDownResourceShadow::where('productivity_ref', $productivity->csi_code)
//                ->whereProjectId(35)->update(['productivity_id' => $productivity->id]);
//
//            $bar->advance();
//        }

//        $bar->finish();

//        $this->output->newLine(2);

        $refs = BreakDownResourceShadow::where('project_id', 35)->selectRaw('DISTINCT productivity_ref')->whereRaw('coalesce(productivity_ref, "") != ""')->pluck('productivity_ref');
        $bar = $this->output->createProgressBar($refs->count());
        foreach ($refs as $ref) {
            $prod = Productivity::where('project_id', 35)->where('csi_code', $ref)->first();
            if (!$prod) {
                $prod = Productivity::whereNull('project_id')->where('csi_code', $ref)->first();
            }

            BreakDownResourceShadow::where('productivity_ref', $ref)
                ->whereProjectId(35)->update(['productivity_id' => $prod->id]);

            $bar->advance();
        }

        $bar->finish();

    }
}
