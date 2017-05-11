<?php

namespace App\Console\Commands;

use App\Boq;
use App\MasterShadow;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

class AddBoqToMasterSahdow extends Command
{
    protected $signature = 'master-shadow:add-boq';

    /** @var  ProgressBar */
    protected $bar;

    /** @var Collection */
    protected $boqs;

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $query = MasterShadow::where('boq_id', 0);
        $this->bar = $this->output->createProgressBar($query->count());
        $this->bar->setBarWidth(50);
        $this->boqs = collect();

        $query->with('wbs_level')->chunk(10000, function (Collection $shadows) {
            $shadows->each(function (MasterShadow $shadow) {
                $code = $shadow->wbs_level->code . '#' . $shadow->cost_account;
                if ($this->boqs->has($code)) {
                    $boq = $this->boqs->get($code);
                } else {
                    $boq = Boq::costAccountOnWbs($shadow->wbs_level, $shadow->cost_account)->first();
                    $this->boqs->put($code, $boq);
                }

                if ($boq) {
                    $shadow->boq_id = $boq->id;
                    $shadow->boq_wbs_id = $boq->wbs_id;
                } else {
                    $shadow->boq_id = 0;
                    $shadow->boq_wbs_id = 0;
                }

                $shadow->save();
                $this->bar->advance();
            });
        });

        $this->bar->finish();
    }
}
