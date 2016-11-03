<?php
/**
 * Created by PhpStorm.
 * User: omar.garana
 * Date: 11/3/2016
 * Time: 12:53 PM
 */

namespace App\Http\ViewComposers;


use App\Jobs\CacheBoqTree;
use App\Project;
use Illuminate\View\View;

class BoqComposer
{
    function compose(View $view)
    {
        if ($view->project) {
            $project = $view->project;
        } else {
            $project = Project::find($view->project_id);
        }

        ini_set('memory_limit', '300M');
        $boqArray = \Cache::remember('boq-' . $project->id, 7*24 * 60, function () use ($project) {
            return dispatch(new CacheBoqTree($project));
        });

        $view->with(compact('boqArray'));
    }

}