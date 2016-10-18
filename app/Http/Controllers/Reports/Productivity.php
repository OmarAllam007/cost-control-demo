<?php
/**
 * Created by PhpStorm.
 * User: omar.garana
 * Date: 10/12/2016
 * Time: 11:59 AM
 */

namespace App\Http\Controllers\Reports;


use App\Project;
use App\Unit;

class Productivity
{

    public function getProductivity(Project $project)
    {
        $breakDown_resources = $project->breakdown_resources()->get();
        $data = [];
        foreach ($breakDown_resources as $breakDown_resource) {
            $productivity = $breakDown_resource->productivity;
            if ($productivity != null || $productivity != 0) {
                $category = $productivity->category;
                if ($category != null) {
                    if (!isset($data[ $category->name ])) {
                        $data[ $category->name ] = [
                            'name' => $category->name,
                            'items' => [],
                        ];
                    }
                    if (!isset($data[ $category->name ]['items'][ $productivity->name ])) {
                        $data[ $category->name ]['items'][ $productivity->description ] = [
                            'name' => $productivity->description,
                            'unit' => Unit::find($productivity->unit)->type,
                            'crew_structure' => $productivity->crew_structure,
                            'productivity' => $productivity->after_reduction,
                            'daily_output' => $productivity->daily_output,
                        ];
                    }
                }
            }
        }
//        dd($data);
        return view('reports.productivity', compact('data', 'project'));
    }
}