<?php
/**
 * Created by PhpStorm.
 * User: omar.garana
 * Date: 10/11/2016
 * Time: 2:53 PM
 */

namespace App\Http\Controllers\Reports;


use App\Boq;
use App\BreakDownResourceShadow;
use App\Project;

class BoqPriceList
{
    public function getBoqPriceList(Project $project)
    {


        $breakDown_resources = BreakDownResourceShadow::where('project_id',$project->id)->get();
        $data = [];
        $parents = [];
        foreach ($breakDown_resources as $breakDown_resource) {
            $resource = $breakDown_resource->resource;
            $root = $resource->types->root;
            $wbs_level = $breakDown_resource->wbs;
            $cost_account = $breakDown_resource->breakdown->cost_account;
            $boq = Boq::where('cost_account', $cost_account)->first();
            $description = strtolower($boq->description);
            if (!isset($data[$wbs_level->name])) {
                $data[$wbs_level->name] = ['name' => $wbs_level->name];

            }

            $parent = $wbs_level;
            while ($parent->parent) {
                $parent = $parent->parent;
                if (!isset($data[$wbs_level->name]['parents'][$parent->id])) {
                    $data[$wbs_level->name]['parents'][$parent->id] = $parent->name;
                }
            }
            if (!isset($data[$wbs_level->name]['boqs'][$description])) {
                $data[$wbs_level->name]['boqs'][$description] = [];
            }


            if (!isset($data[$wbs_level->name]['boqs'][$description]['items'][$cost_account])) {
                $data[$wbs_level->name]['boqs'][$description]['items'][$cost_account] = [
                    'id' => $boq->id,
                    'cost_account' => $cost_account,
                    'unit' => isset($breakDown_resource->breakdown->qty_survey->unit->type) ? $breakDown_resource->breakdown->qty_survey->unit->type : '',
                    'LABORS' => 0,
                    'MATERIAL' => 0,
                    'Subcontractors' => 0,
                    'EQUIPMENT' => 0,
                    'SCAFFOLDING' => 0,
                    'OTHERS' => 0,
                    'total_resources' => 0,
                ];
            }

            $name = substr($root->name, strpos($root->name, '.') + 1);

            $data[$wbs_level->name]['boqs'][$description]['items'][$cost_account][$name] += $breakDown_resource->boq_unit_rate;
            $data[$wbs_level->name]['boqs'][$description]['items'][$cost_account]['total_resources'] += $breakDown_resource->boq_unit_rate;


        }


        ksort($data);
        foreach ($data as $key => $value) {
            foreach ($value['parents'] as $pKey => $pValue) {
                if(in_array($pValue,$parents)){
                    unset($data[$key]['parents'][$pKey]);
                    continue;
                }
                $parents[] = $pValue;
                ksort($data[$key]['parents']);
            }
        }

        return view('reports.boq_price_list', compact('project', 'data'));
    }


}

