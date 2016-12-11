<?php
/**
 * Created by PhpStorm.
 * User: omar.garana
 * Date: 11/1/2016
 * Time: 10:10 AM
 */

namespace App\Http\ViewComposers;


use App\Jobs\CacheCsiCategoryTree;
use Illuminate\View\View;

class CsiCategoryComposer
{
    function compose(View $view)
    {
        $categoryTree = \Cache::remember('csi-tree', 7 * 24 * 60, function () {
            return dispatch(new CacheCsiCategoryTree());
        });
        $view->with(compact('categoryTree'));
    }

}