<?php
/**
 * Created by PhpStorm.
 * User: omar.garana
 * Date: 10/10/2016
 * Time: 11:44 AM
 */

namespace App\Http\Controllers\Reports;


use App\Boq;
use App\Project;
use App\Survey;
use Khill\Lavacharts\Lavacharts;

class RevisedBoq
{
    public function getRevised(Project $project)
    {
        $resources = $project->breakdown_resources()->get();
        $data = [];
        $total = [
            'revised_boq' => 0,
            'original_boq' => 0,
            'weight' => 0,
        ];
        foreach ($resources as $break_down_resource) {
            $wbs = $break_down_resource->breakdown->wbs_level;
            if (!isset($data[ $wbs->id ])) {
                $data[ $wbs->id ] = [
                    'code' => $wbs->code,
                    'name' => $wbs->name,
                    'cost_account' => [],
                    'revised_boq' => 0,
                    'original_boq' => 0,
                    'weight' => 0
                ];
            }
            if (!isset($data[ $wbs->id ]['cost_account'][ $break_down_resource->breakdown->cost_account ])) {
                $data[ $wbs->id ]['cost_account'][ $break_down_resource->breakdown->cost_account ] = [
                    'revised_boq' => 0,
                    'original_boq' => 0,
                    'weight' => 0,];

                $boq = Boq::where('cost_account', $break_down_resource->breakdown->cost_account)->first();
                $survey = Survey::where('cost_account', $break_down_resource->breakdown->cost_account)->first();

                $data[ $wbs->id ]['cost_account'][ $break_down_resource->breakdown->cost_account ]['original_boq'] = $boq->price_ur * $boq->quantity;

                $data[ $wbs->id ]['cost_account'][ $break_down_resource->breakdown->cost_account ]['revised_boq'] = $boq->price_ur * $survey->eng_qty;
            }

        }
        foreach ($data as $key => $value) {
            foreach ($value['cost_account'] as $item) {
                $data[$key]['revised_boq']+=$item['revised_boq'];
                $data[$key]['original_boq']+=$item['original_boq'];
                $total['revised_boq']+=$item['revised_boq'];
                $total['original_boq']+=$item['original_boq'];
            }
        }


        foreach ($data as $key => $value) {
            if ($data[ $key ]['original_boq']) {
                $data[ $key ]['weight'] += (($data[ $key ]['revised_boq'] / $data[ $key ]['original_boq']));
                $total['weight'] += $data[ $key ]['weight'];
            }
        }
        $chart = $this->getRevisedChart($data);
        return view('reports.revised_boq', compact('data', 'total', 'project', 'chart'));
    }

    public function getRevisedChart($data)
    {
        $revised_boqs = \Lava::DataTable();
        $revised_boqs->addStringColumn('Boqs')->addNumberColumn('Weight');
        foreach ($data as $key => $value) {
            $revised_boqs->addRow([$data[ $key ]['name'], $data[ $key ]['weight']]);
        }
        \Lava::PieChart('BOQ', $revised_boqs, [
            'width' => '1000',
            'height' => '600',
            'title' => 'REVISED BOQ',
            'is3D' => true,
            'slices' => [
                ['offset' => 0.0],
                ['offset' => 0.0],
                ['offset' => 0.0],
            ],
            'pieSliceText' => "value",
        ]);
    }
}