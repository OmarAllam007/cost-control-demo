<?php

namespace App\Console\Commands;

use App\BreakdownResource;
use App\BreakDownResourceShadow;
use App\WbsLevel;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Symfony\Component\Console\Helper\ProgressBar;

class RebuildModShadow extends Command
{
    protected $signature = 'fix-mod';

    protected $description = '';

    /** @var ProgressBar */
    protected $bar;

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $wbsIds = WbsLevel::find(9733)->getChildrenIds();
        $count = BreakDownResourceShadow::whereProjectId(40)->whereIn('wbs_id', $wbsIds)->count();
        $this->output->comment($count . ' Resources found');
        $this->bar = $this->output->createProgressBar($count);

        BreakDownResourceShadow::whereProjectId(40)->whereIn('wbs_id', $wbsIds)->with('breakdown_resource')->chunk(10000, function(Collection $shadows) {
            $shadows->pluck('breakdown_resource')->each(function(BreakdownResource $resource) {
                $resource->updateShadow();
                $this->bar->advance();
            });
        });

        $this->bar->finish();
    }
}
