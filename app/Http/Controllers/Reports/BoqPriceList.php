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


        $breakDown_resources = BreakDownResourceShadow::where('project_id', $project->id)->with('resource', 'wbs', 'breakdown', 'std_activity')->get();
        $data = [];
        $parents = [];
        /** @var BreakDownResourceShadow $breakDown_resource */
        foreach ($breakDown_resources as $breakDown_resource) {
            $root = $breakDown_resource['resource_type'];
            if(isset($breakDown_resource->wbs)){
                $wbs_level = $breakDown_resource->wbs;
                $cost_account = $breakDown_resource['cost_account'];
                $boq = Boq::where('cost_account', $cost_account)->first();
                $description = strtolower($boq->description);
                if (!isset($data[$breakDown_resource['wbs_id']])) {
                    $data[$breakDown_resource['wbs_id']] = ['name' => $breakDown_resource->wbs->name];

                }

                $parent = $wbs_level;
                while ($parent->parent) {
                    $parent = $parent->parent;
                    if (!isset($data[$breakDown_resource['wbs_id']]['parents'][$parent->id])) {
                        $data[$breakDown_resource['wbs_id']]['parents'][$parent->id] = $parent->name;
                    }
                }
                if (!isset($data[$breakDown_resource['wbs_id']]['boqs'][$description])) {
                    $data[$breakDown_resource['wbs_id']]['boqs'][$description] = [];
                }


                if (!isset($data[$breakDown_resource['wbs_id']]['boqs'][$description]['items'][$cost_account])) {
                    $data[$breakDown_resource['wbs_id']]['boqs'][$description]['items'][$cost_account] = [
                        'id' => $boq->id,
                        'cost_account' => $cost_account,
                        'unit' => $breakDown_resource['measure_unit'],
                        'LABORS' => 0,
                        'MATERIAL' => 0,
                        'SUBCONTRACTORS' => 0,
                        'EQUIPMENT' => 0,
                        'SCAFFOLDING' => 0,
                        'OTHERS' => 0,
                        'total_resources' => 0,
                    ];
                }

                $name = mb_strtoupper(substr($root, strpos($root, '.') + 1));

                $data[$breakDown_resource['wbs_id']]['boqs'][$description]['items'][$cost_account][$name] += $breakDown_resource['boq_equivilant_rate'];
                $data[$breakDown_resource['wbs_id']]['boqs'][$description]['items'][$cost_account]['total_resources'] += $breakDown_resource['boq_equivilant_rate'];

            }


        }


        ksort($data);
        foreach ($data as $key => $value) {
            foreach ($value['parents'] as $pKey => $pValue) {
                if (in_array($pValue, $parents)) {
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

