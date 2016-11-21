<?php
/**
 * Created by PhpStorm.
 * User: omar.garana
 * Date: 10/10/2016
 * Time: 11:44 AM
 */

namespace App\Http\Controllers\Reports;


use App\Boq;
use App\Breakdown;
use App\Project;
use App\Survey;
use Khill\Lavacharts\Lavacharts;

class RevisedBoq
{
    public function getRevised(Project $project)
    {

        $breakdowns = $project->breakdowns()->with('wbs_level', 'resources')->get();
        $data = [];
        $total = [
            'revised_boq' => 0,
            'original_boq' => 0,
            'weight' => 0,
        ];
        foreach ($breakdowns as $breakdown) {
            $wbs_level = $breakdown->wbs_level;
            $dry = $breakdown->getDry($wbs_level->id);

            if ($dry) {
                if (!isset($data[$wbs_level->id])) {
                    $data[$wbs_level->id] = [
                        'code' => $wbs_level->code,
                        'name' => $wbs_level->name,
                        'cost_account' => [],
                        'revised_boq' => 0,
                        'original_boq' => 0,
                        'weight' => 0,
                    ];
                }

                if (!isset($data[$wbs_level->id]['cost_account'][$breakdown->cost_account])) {
                    $data[$wbs_level->id]['cost_account'][$breakdown->cost_account] = [
                        'revised_boq' => 0,
                        'original_boq' => 0,
                        'weight' => 0,
                    ];

                    $boq = Boq::where('cost_account', $breakdown->cost_account)->first();
                    $survey = Survey::where('cost_account', $breakdown->cost_account)->first();

                    if ($boq && $survey) {
                        $data[$wbs_level->id]['cost_account'][$breakdown->cost_account]['original_boq'] += $boq->price_ur * $boq->quantity;
                        $data[$wbs_level->id]['cost_account'][$breakdown->cost_account]['revised_boq'] += $boq->price_ur * $survey->eng_qty;
                    }

                }


            } else {

                $parent = $wbs_level;
                while ($parent->parent) {
                    $parent = $parent->parent;
                    $parent_dry = $breakdown->getDry($parent->id);
                    if ($parent_dry) {
                        if (!isset($data[$parent->id])) {
                            $data[$parent->id] = [
                                'code' => $parent->code,
                                'name' => $parent->name,
                                'cost_account' => [],
                                'revised_boq' => 0,
                                'original_boq' => 0,
                                'weight' => 0,
                            ];
                        }

                        $boq_parent   = Boq::where('wbs_id',$parent->id)->where('cost_account',$breakdown->cost_account)->sum(\DB::raw('quantity * price_ur'));
                        $boq_price   = Boq::where('wbs_id',$parent->id)->where('cost_account',$breakdown->cost_account)->sum('price_ur');
                        $survey_parent = Survey::where('wbs_level_id',$parent->id)->where('cost_account',$breakdown->cost_account)->sum('eng_qty');
                        $data[$parent->id]['original_boq'] = $boq_parent;
                        $data[$parent->id]['revised_boq'] = $boq_price*$survey_parent;
                        break;
                    }
                }
            }
        }



        foreach ($data as $key => $value) {
            foreach ($value['cost_account'] as $item) {
                $data[$key]['revised_boq'] += $item['revised_boq'];
                $data[$key]['original_boq'] += $item['original_boq'];
                $total['revised_boq'] += $item['revised_boq'];
                $total['original_boq'] += $item['original_boq'];
            }
        }
        foreach ($data as $key => $value) {
            if ($data[$key]['original_boq']) {
                $data[$key]['weight'] += $data[$key]['revised_boq'] / $data[$key]['original_boq'] * 100;
            }
        }
        $total['weight'] = ($total['revised_boq'] / $total['original_boq']) * 100;
        $chart = $this->getRevisedChart($data);
        return view('reports.revised_boq', compact('data', 'total', 'project', 'chart'));
    }

    public function getRevisedChart($data)
    {
        $revised_boqs = \Lava::DataTable();
        $revised_boqs->addStringColumn('Boqs')->addNumberColumn('Weight');
        foreach ($data as $key => $value) {
            $revised_boqs->addRow([$data[$key]['name'], $data[$key]['weight']]);
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