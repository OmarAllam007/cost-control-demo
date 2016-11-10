<?php

namespace App\Http\Controllers\Api;

use App\Boq;
use App\BreakdownResource;
use App\Formatters\BreakdownResourceFormatter;
use App\Jobs\CacheWBSTree;
use App\Project;
use App\Survey;
use App\WbsLevel;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class WbsController extends Controller
{
    function index(Project $project)
    {
        $wbsTree = \Cache::remember('wbs-tree-' . $project->id, 7 * 24 * 60, function () use ($project) {
            return dispatch(new CacheWBSTree($project));
        });
        return $wbsTree;
    }

    function breakdowns(WbsLevel $wbs_level)
    {
        $resources = BreakdownResource::forWbs($wbs_level->id)->get()
            ->map(function (BreakdownResource $res) {
                return new BreakdownResourceFormatter($res);
            });

        return $resources;
    }

    function boq(WbsLevel $wbs_level)
    {
        return Boq::where('wbs_id', $wbs_level->id)->get()->groupBy('type');
    }

    function qtySurvey(WbsLevel $wbs_level)
    {
        return Survey::where('wbs_level_id', $wbs_level->id)->get();
    }
}
