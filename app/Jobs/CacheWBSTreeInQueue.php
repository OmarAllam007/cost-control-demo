<?php
namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CacheWBSTreeInQueue extends CacheWBSTree implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle()
    {
        $start = microtime(1);
        $tree = parent::handle();
        $key = 'wbs-tree-' . $this->project->id;
        \Cache::forget($key);
        \Cache::put($key, $tree);
        \Log::info('Cache WBS done in ' . round(microtime(1) - $start, 4) . ' seconds');
    }
}