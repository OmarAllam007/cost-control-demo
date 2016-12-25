<?php
/**
 * Created by PhpStorm.
 * User: omar
 * Date: 12/12/16
 * Time: 01:38 م
 */

namespace App\Observers;


use App\BreakdownResource;
use App\BreakDownResourceShadow;
use App\Http\Controllers\Caching\ResourcesCache;
use App\Project;
use App\Resources;
use App\Unit;

class ResourcesObserver
{
    function created(Resources $resource)
    {
        $cache = new ResourcesCache();
        $cache->cacheResources();
    }

    function creating(Resources $resource)
    {
        if (!$resource->project_id) {
            $this->generateResourceCode($resource);
        }
    }

    function updated(Resources $resource)
    {
        $resource->updateBreakdownResources();
        $cache = new ResourcesCache();
        $cache->cacheResources();

    }

    function saving(Resources $resource)
    {
//        $cache = new ResourcesCache();
//        $cache->cacheResources();
    }

    function deleted(Resources $resource)
    {
        $cache = new ResourcesCache();
        $cache->cacheResources();
    }

    public function generateResourceCode($resource)
    {
        $rootName = substr($resource->types->root->name, strpos($resource->types->root->name, '.') + 1, 1);

        $names = explode('»', $resource->types->path);
        $code = [];
        $code [] = $rootName;
        //if Labors get by letter else by number
        if ($rootName != 'L') {
            foreach ($names as $key => $name) {
                if ($key == 0) {
                    continue;
                }

                $name = trim($name);
                $divname = substr($name, 0, strpos($resource->types->root->name, '.'));
                $code [] = $divname;

            }
        } else {
            foreach ($names as $key => $name) {
                if ($key == 0) {
                    continue;
                }
                $name = trim($name);
                $divname = substr($name, strpos($resource->types->root->name, '.') + 1, 1);
                $code [] = $divname;

            }
        }

        $resourceNumber = Resources::where('resource_type_id', $resource->types->id)->count();
        $resourceNumber++;
        $code[] = $resourceNumber <= 10 ? '0' . $resourceNumber : $resourceNumber;
        $finalCode = implode('.', $code);

        $resource->resource_code = $finalCode;
    }

}