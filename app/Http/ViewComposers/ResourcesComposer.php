<?php
/**
 * Created by PhpStorm.
 * User: hazem
 * Date: 10/31/16
 * Time: 1:04 PM
 */

namespace App\Http\ViewComposers;


use App\Http\Controllers\Caching\ResourcesCache;
use Illuminate\View\View;

class ResourcesComposer
{
    function compose(View $view)
    {
        $resourcesTree = (new ResourcesCache())->cacheResources(false);
        $view->with(compact('resourcesTree'));
    }
}