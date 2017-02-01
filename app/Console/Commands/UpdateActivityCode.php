<?php

namespace App\Console\Commands;

use App\BreakdownResource;
use App\BreakDownResourceShadow;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Symfony\Component\Console\Helper\ProgressBar;

class UpdateActivityCode extends Command
{
    protected $signature = 'update-activity-code';

    protected $description = 'Update activity code';

    /**
     * @var ProgressBar
     */
    protected $bar;

    public function handle()
    {
        $query = BreakDownResourceShadow::whereRaw('code not in (select activity_code from activity_maps)');
        $this->bar = $this->output->createProgressBar($query->count());

        BreakdownResource::flushEventListeners();
        BreakDownResourceShadow::flushEventListeners();

        $query->with('std_activity')->with('breakdown_resource')->with('wbs')->chunk(5000, function (Collection $shadows) {
            $shadows->each(function(BreakDownResourceShadow $shadow) {
                $code = $shadow->wbs->code . $shadow->std_activity->id_partial;
                if ($code != $shadow->code) {
                    $shadow->update(compact('code'));
                    $shadow->breakdown_resource->update(compact('code'));
                }

                $this->bar->advance();
            });
        });

        $this->bar->finish();
    }
}
