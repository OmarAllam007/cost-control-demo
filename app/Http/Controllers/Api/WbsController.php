<?php

namespace App\Http\Controllers\Api;

use App\Boq;
use App\BreakdownResource;
use App\BreakDownResourceShadow;
use App\Formatters\BreakdownResourceFormatter;
use App\Jobs\CacheWBSTree;
use App\Project;
use App\Survey;
use App\WbsLevel;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

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
        $resources = BreakDownResourceShadow::where('wbs_id', $wbs_level->id)->orderBy('activity')->orderBy('cost_account')->get();
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

    function tree_by_resource($project)
    {
        $breakdownResources = BreakDownResourceShadow::with(['wbs', 'resource', 'std_activity'])
            ->orderBy('resource_code')
            ->where('project_id', $project)->get();

        $resources = [];
        foreach ($breakdownResources as $resource) {
            $code = $resource->resource_code;
            if (!isset($resources[$code])) {
                $resources[$code] = ['name' => $resource->resource_name];
            }

            $wbs_id = $resource->wbs_id;
            if (empty($resources[$code][$wbs_id])) {
                $resources[$code][$wbs_id] = ['code' => $resource->wbs->code, 'name' => $resource->wbs->name, 'activities' => []];
            }

            $activity_id = $resource->std_activity->id;
            $resources[$code][$wbs_id]['activities'][$activity_id] = $resource->std_activity->name;
        }

        return $resources;
    }

    function tree_by_wbs($project)
    {
        $this->breakdownResources = BreakDownResourceShadow::with(['wbs', 'resource', 'std_activity'])
            ->where('project_id', $project)
            ->orderBy('activity')->orderBy('resource_name')
            ->get()
            ->groupBy('wbs_id');


        $wbsTree = collect(\Cache::remember('wbs-tree-' . $project, 7 * 24 * 60, function () use ($project) {
            return dispatch(new CacheWBSTree(Project::find($project)));
        }))->map([$this, 'appendActivities']);

        return $wbsTree;
    }

    function appendActivities($item)
    {
        $item['activities'] = [];
        if ($this->breakdownResources->has($item['id'])) {
            $breakdowns = $this->breakdownResources->get($item['id']);
            foreach ($breakdowns as $resource) {
                if (empty($item['activities'][$resource->activity_id])) {
                    $item['activities'][$resource->activity_id] = [
                        'id' => $resource->activity_id, 'name' => $resource->std_activity->name,
                        'resources' => []
                    ];
                }

                $item['activities'][$resource->activity_id]['resources'][$resource->resource_code] = $resource->resource->name;
            }
        }

        $item['children'] = collect($item['children'])->map([$this, 'appendActivities']);

        return $item;
    }
}
