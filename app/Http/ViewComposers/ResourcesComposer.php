<?php
/**
 * Created by PhpStorm.
 * User: hazem
 * Date: 10/31/16
 * Time: 1:04 PM
 */

namespace App\Http\ViewComposers;


use App\Jobs\CacheResourcesTree;
use Illuminate\View\View;

class ResourcesComposer
{
    function compose(View $view)
    {
        $resourcesTree = \Cache::remember('resources-tree', 7 * 24 * 60, function(){
            return dispatch(new CacheResourcesTree());
        });

        $view->with(compact('resourcesTree'));
    }
}