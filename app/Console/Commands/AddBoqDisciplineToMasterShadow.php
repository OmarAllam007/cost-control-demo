<?php

namespace App\Console\Commands;

use App\Boq;
use App\MasterShadow;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Symfony\Component\Console\Helper\ProgressBar;

class AddBoqDisciplineToMasterShadow extends Command
{
    protected $signature = 'master-shadow:add-boq-discipline';

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
        $this->bar = $this->output->createProgressBar(MasterShadow::count());
        $this->bar->setBarWidth(50);
        $this->boqs = collect();

        MasterShadow::chunk(10000, function (Collection $shadows) {
            $shadows->each(function (MasterShadow $shadow) {
                $code = $shadow->wbs_level->code . '#' . $shadow->cost_account;
                if ($this->boqs->has($code)) {
                    $boq = $this->boqs->get($code);
                } else {
                    $boq = Boq::costAccountOnWbs($shadow->wbs_level, $shadow->cost_account)->first();
                    $this->boqs->put($code, $boq);
                }

                if ($boq) {
                    $shadow->boq_discipline = $boq->type;
                } else {
                    $shadow->boq_discipline = '';
                }

                $shadow->save();
                $this->bar->advance();
            });
        });

        $this->bar->finish();
    }
}
