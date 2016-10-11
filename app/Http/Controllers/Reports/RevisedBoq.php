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

class RevisedBoq
{
    public function getRevised(Project $project)
    {
        $break_downs = $project->breakdowns()->get();
        $data = [];
        $total = [
            'revised_boq' => 0,
            'original_boq' => 0,
            'weight' => 0,
        ];
        foreach ($break_downs as $break_down) {
            $boqs = Boq::where('cost_account', $break_down->cost_account);
            $wbs = $break_down->wbs_level;
            if (!isset($data[ $wbs->id ])) {
                $data[ $wbs->id ] = [
                    'code' => $wbs->code,
                    'name' => $wbs->name,
                    'revised_boq' => 0,
                    'original_boq' => 0,
                    'weight' => 0,
                ];

                $data[ $wbs->id ]['original_boq'] += $boqs->sum(\DB::raw('price_ur * quantity'));

                $eng_qty = Survey::where('cost_account', $break_down->cost_account)->sum(\DB::raw('eng_qty'));
                $price_ur = Boq::where('cost_account', $break_down->cost_account)->sum(\DB::raw('price_ur'));
                $data[ $wbs->id ]['revised_boq'] += $price_ur * $eng_qty;
            }
        }
        foreach ($data as $key => $value) {
            $total['revised_boq'] += $data[ $key ]['revised_boq'];
            $total['original_boq'] += $data[ $key ]['original_boq'];
        }
        foreach ($data as $key => $value) {
            $data[ $key ]['weight'] +=(($data[$key]['revised_boq'] / $data[$key]['original_boq']) * 100);
            $total['weight'] += $data[$key]['weight'];
        }

        return view('reports.revised_boq', compact('data', 'total'));
    }
}