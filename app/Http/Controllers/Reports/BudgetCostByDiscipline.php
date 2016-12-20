<?php
/**
 * Created by PhpStorm.
 * User: omar.garana
 * Date: 10/9/2016
 * Time: 4:24 PM
 */

namespace App\Http\Controllers\Reports;


use App\Project;
use App\Survey;

class BudgetCostByDiscipline
{
    public function compareBudgetCostDiscipline(Project $project)
    {
        $survey = [];
        $total = [
            'total' => 0,
            'weight_total' => 0,
        ];
        $shadows = $project->shadows()->get();

        foreach ($shadows as $shadow) {
            $qs_items = Survey::where('cost_account', $shadow->cost_account)->get();
            foreach ($qs_items as $qs_item) {
                if (!isset($survey[$shadow->std_activity->discipline])) {
                    $survey[$shadow->std_activity->discipline] = [
                        'code' => $shadow->std_activity->code,
                        'name' => $qs_item->discipline,
                        'budget_cost' => 0,
                        'weight' => 0,
                    ];
                }
            }
            $survey[$shadow->std_activity->discipline]['budget_cost'] += is_nan($shadow->budget_cost) ? 0 : $shadow->budget_cost;

        }
        foreach ($survey as $item) {
            $total['total'] += $item['budget_cost'];
        }
        foreach ($survey as $key => $value) {
            if ($total['total']) {
                $survey[$key]['weight'] += floatval(($survey[$key]['budget_cost'] / $total['total']) * 100);
                $total['weight_total'] += $survey[$key]['weight'];
            }
        }
        $this->compareBudgetCostDisciplineCharts($survey);
        return view('reports.budget_cost_by_discipline', compact('project', 'survey', 'total'));

    }

    public function compareBudgetCostDisciplineCharts($data)
    {
        $costTable = \Lava::DataTable();
        $costTable->addStringColumn('BudgetCost')->addNumberColumn('Discipline');
        foreach ($data as $key => $value) {
            $costTable->addRow([$data[$key]['name'], $data[$key]['budget_cost']]);
        }
        \Lava::ColumnChart('BudgetCost', $costTable, [
            'title' => 'Budget Cost By Discipline',
            'titleTextStyle' => [
                'color' => '#eb6b2c',
                'fontSize' => 14,
                'width' => '1000',
                'height' => '600',

            ],

        ]);


        $building = \Lava::DataTable();
        $building->addStringColumn('Buildings')->addNumberColumn('Weight');
        foreach ($data as $key => $value) {
            $building->addRow([$data[$key]['name'], $data[$key]['weight']]);
        }
        \Lava::PieChart('Cost', $building, [
            'width' => '1000',
            'height' => '600',
            'title' => '% Weight From Budget',
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