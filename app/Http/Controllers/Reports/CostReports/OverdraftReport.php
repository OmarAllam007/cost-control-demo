<?php
/**
 * Created by PhpStorm.
 * User: omar
 * Date: 27/12/16
 * Time: 11:19 ص
 */

namespace App\Http\Controllers\Reports\CostReports;


use App\CostShadow;
use App\Project;
use App\WbsLevel;

class OverdraftReport
{
    protected $project;

    function getDraft(Project $project)
    {
        $this->project = $project;
        $wbs_levels = WbsLevel::where('project_id', $project->id)->tree()->get();
        $tree = [];
        foreach ($wbs_levels as $wbs_level) {
            $levelTree = $this->buildTree($wbs_level);
            $tree[] = $levelTree;
        }
        dd($tree);
        return $tree;
    }

    protected function buildTree(WbsLevel $wbs_level)
    {
        $tree = ['id' => $wbs_level->id, 'name' => $wbs_level->name, 'children' => [], 'divisions' => [], 'data' => []];

        $costShadow = CostShadow::where('project_id', $this->project->id)->where('wbs_level_id', $wbs_level->id)->get();

        foreach ($costShadow as $shadow) {
            $division = $shadow->budget->std_Activity->division;
            if (!isset($tree['divisions'][$division->id])) {
                $tree['divisions'][$division->id] = [
                    'name' => $division->name,
                    'data' => [],
                ];
            };
        }
        if ($wbs_level->children()->count()) {
            $tree['children'] = $wbs_level->children->map(function (WbsLevel $childLevel) {
                return $this->buildTree($childLevel);
            });
        }


        return $tree;
    }

}