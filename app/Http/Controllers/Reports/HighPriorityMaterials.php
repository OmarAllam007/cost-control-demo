<?php
/**
 * Created by PhpStorm.
 * User: omar.garana
 * Date: 10/12/2016
 * Time: 8:06 AM
 */

namespace App\Http\Controllers\Reports;


use App\BreakDownResourceShadow;
use App\Project;
use App\Resources;
use App\ResourceType;
use App\Unit;
use Illuminate\Http\Request;
use Make\Makers\Resource;

class HighPriorityMaterials
{
    private $project;

    public function getTopHighPriorityMaterials(Project $project, Request $request)
    {
        set_time_limit(300);

        $tree = [];
        $this->project = $project;
        $resource_types = ResourceType::tree()->with('children', 'children.children', 'children.children.children')->get();
        $types = $resource_types->where('name', '03.MATERIAL');
        foreach ($types as $type) {
            $level = $this->getTree($type);
            $tree[] = $level;
        }
        return view('reports.budget.high_priority_materials.get_resource_types', compact('tree', 'project'));
    }

    private function getTree($type)
    {
        $tree = ['id' => $type->id, 'name' => $type->name, 'children' => [], 'resources' => [], 'budget_cost' => 0, 'budget_unit' => 0];
        $shadows = \DB::select('SELECT
  r.name AS resource_name,
  r.id AS resource_id,
  r.resource_type_id,
  t.name type_name,
  sum(sh.budget_cost) AS budget_cost , 
  sum(sh.budget_unit) AS budget_unit
FROM resources r, break_down_resource_shadows sh, resource_types t
WHERE sh.project_id = ?
      AND r.id = sh.resource_id
      AND t.id = r.resource_type_id
      AND t.id = ?
GROUP BY r.name, r.resource_type_id  , t.name , r.id', [$this->project->id, $type->id]);

        foreach ($shadows as $shadow) {
            if (!isset($tree['resources'][$shadow->resource_name])) {
                $tree['resources'][$shadow->resource_name] = [
                    'id' => $shadow->resource_id,
                    'name' => $shadow->resource_name,
                    'budget_cost' => $shadow->budget_cost,
                    'budget_unit' => $shadow->budget_unit,
                ];
                $tree['budget_cost'] += $shadow->budget_cost;
                $tree['budget_unit'] += $shadow->budget_unit;
            }
        }


        if ($type->children->count()) {
            $tree['children'] = $type->children->map(function (ResourceType $childLevel) {
                return $this->getTree($childLevel);
            });

            foreach ($tree['children'] as $child) {
                $tree['budget_cost'] += $child['budget_cost'];
            }
        }
        return $tree;
    }


}