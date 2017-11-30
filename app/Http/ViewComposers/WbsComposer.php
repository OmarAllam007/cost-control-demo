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
use Carbon\Carbon;
use Illuminate\Support\Fluent;
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

        $this->wbs_levels = $project->wbs_levels->groupBy('parent_id');

        $wbsTree = $this->buildTree();

        $view->with(compact('wbsTree'));
    }

    private function buildTree($parent = 0)
    {
        return $this->wbs_levels->get($parent, collect())->map(function ($level) {
            $l = new Fluent($level->getAttributes());
            $l->children = $this->buildTree($level->id);
            return $l;
        });
    }
}