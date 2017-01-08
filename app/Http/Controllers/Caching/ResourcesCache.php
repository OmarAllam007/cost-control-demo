<?php
namespace App\Http\Controllers\Caching;

use App\Http\Controllers\Controller;
use App\Jobs\CacheResourcesTree;

class ResourcesCache
{
    public function cacheResources($forget = true)
    {
        set_time_limit(120);

        if ($forget) {
            \Cache::forget('resources-tree');
        }

        $resourcesTree = \Cache::remember('resources-tree', 7 * 24 * 60, function () {
            return dispatch(new CacheResourcesTree());
        });

        return $resourcesTree;

    }

}
