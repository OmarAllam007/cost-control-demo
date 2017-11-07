<?php

namespace App\Http\ViewComposers;

use App\Support\ResourceTypesTree;
use Illuminate\View\View;

class ResourcesComposer
{
    function compose(View $view)
    {
        $resourcesTree = app(ResourceTypesTree::class)->get();

        $view->with(compact('resourcesTree'));
    }
}