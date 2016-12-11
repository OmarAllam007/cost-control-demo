<?php
/**
 * Created by PhpStorm.
 * User: omar.garana
 * Date: 10/31/2016
 * Time: 3:00 PM
 */

namespace App\Http\ViewComposers;


use App\CsiCategory;
use App\Jobs\CacheWBSTree;
use App\Project;
use App\WbsLevel;
use Illuminate\View\View;

class WbsComposer
{
    function compose(View $view)
    {
        if ($view->project) {
            $project = $view->project;
        } else {
            $project = Project::find($view->project_id);
        }
        $wbsTree = \Cache::remember('wbs-tree-' . $project->id, 7 * 24 * 60, function () use ($project) {
            return dispatch(new CacheWBSTree($project));
        });
        $view->with(compact('wbsTree'));
    }
}