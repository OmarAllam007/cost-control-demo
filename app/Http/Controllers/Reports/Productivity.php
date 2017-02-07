<?php
/**
 * Created by PhpStorm.
 * User: omar.garana
 * Date: 10/12/2016
 * Time: 11:59 AM
 */

namespace App\Http\Controllers\Reports;


use App\CsiCategory;
use App\Project;
use App\Unit;

class Productivity
{

    private $productivityIds;
    private $productivity;
    private $units;
    private $project;

    public function getProductivity(Project $project)
    {
        set_time_limit(300);
        $this->productivityIds = collect();
        $this->project = $project;

        collect(\DB::select('SELECT DISTINCT sh.productivity_id  from break_down_resource_shadows sh
WHERE sh.project_id=' . $project->id . '
AND sh.productivity_id !=0'))->map(function ($id) {
            $this->productivityIds->push($id->productivity_id);
        })->unique();
        $this->units = Unit::all()->keyBy('id')->map(function ($unit){return $unit->type;});

        $this->productivity = CsiCategory::all()->keyBy('id')->map(function ($category){
            return $category->productivity->whereIn('id',$this->productivityIds->toArray());
        });

        $tree = [];
        $csi_levels = CsiCategory::tree()->get();
        foreach ($csi_levels as $level) {
            $level_tree = $this->buildTree($level);
            $tree [] = $level_tree;
        }
        $tree = collect($tree)->sortBy('name')->toArray();
        return view('reports.budget.productivity.productivity', compact('tree', 'project'));
    }

    private function buildTree($level)
    {
        $tree = ['id' => $level->id, 'name' => $level->name, 'children' => [], 'productivities' => []];
        if ($level->children->count()) {
            $tree['children'] = $level->children->map(function (CsiCategory $childLevel) {
                return $this->buildTree($childLevel);
            });
        }

        $tree['children'] = collect($tree['children'])->sort()->toArray();

        if ($this->productivity->get($level->id)->count()) {
            $tree['productivities'] = $this->productivity->get($level->id)->map(function ($productivity) {
                return ['id' => $productivity->id,
                    'description' => $productivity->description,
                    'csi_code' => $productivity->csi_code,
                    'crew_structure' => $productivity->crew_structure,
                    'unit' => $this->units->get($productivity->unit),
                    'after_reduction' => $productivity->versionFor($this->project->id)->after_reduction,
                ];
            });
        }
        return $tree;
    }
}