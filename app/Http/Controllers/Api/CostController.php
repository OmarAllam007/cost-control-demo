<?php

namespace App\Http\Controllers\Api;

use App\CostResource;
use App\Http\Controllers\Controller;
use App\Project;
use App\WbsLevel;
use App\WbsResource;
use Illuminate\Database\Eloquent\Collection;

class CostController extends Controller
{
    function breakdowns(WbsLevel $wbs_level)
    {
        return WbsResource::where('wbs_level_id', $wbs_level->id)->where('period_id', $wbs_level->project->open_period()->id)
            ->joinShadow()
            ->get();
    }

    function resources(Project $project)
    {
        return CostResource::where('project_id', $project->id)
            ->where('period_id', $project->open_period()->id)
            ->get()->map(function (CostResource $resource) {
                return $resource->jsonFormat();
            });

    }
}
