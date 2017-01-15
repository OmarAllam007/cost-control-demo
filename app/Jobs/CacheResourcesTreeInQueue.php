<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class CacheResourcesTreeInQueue extends CacheResourcesTree implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    public function handle()
    {
        $tree = parent::handle();
        \Cache::forget('resources-tree');
        \Cache::put('resources-tree', $tree, 7 * 24 * 60);
    }
}
