<?php
namespace App\Http\Controllers\Caching;
use App\Http\Controllers\Controller;
class ResourcesCache
{
    public function cacheResources(){

        \Cache::forget('resources-tree');
        $resourcesTree = \Cache::remember('resources-tree', 7 * 24 * 60, function(){
            return dispatch(new \App\Jobs\CacheResourcesTree());
        });

        return $resourcesTree ;

    }

}
