<?php
/**
 * Created by PhpStorm.
 * User: omar.garana
 * Date: 10/9/2016
 * Time: 4:24 PM
 */

namespace App\Http\Controllers\Reports;


use App\BreakDownResourceShadow;
use App\Project;
use App\Survey;

class BudgetCostByDiscipline
{
    private $survies;

    public function compareBudgetCostDiscipline(Project $project)
    {
        set_time_limit(600);
        $survey = [];
        $totalWeight = 0;
        $total = BreakDownResourceShadow::where('project_id', $project->id)->sum('budget_cost');
//        $this->survies = Survey::where('project_id', $project->id)->get()->keyBy('cost_account')->map(function ($survey) {
//            return $survey->discipline;
//        });
        $shadows = $project->shadows()->with('std_activity')->get();
        foreach ($shadows as $shadow) {
            $discpline = $shadow->std_activity->discipline;
            if (!isset($survey[$discpline])) {
                $survey[$discpline] = [
                    'code' => $shadow->std_activity->code,
//                    'name' => $this->survies->get($shadow['cost_account']),
                    'budget_cost' => 0,
                    'weight' => 0,
                ];
            }
            $budget_cost = $shadow->budget_cost;
            $survey[$discpline]['budget_cost'] += is_nan($budget_cost) ? 0 : $budget_cost;
        }

        foreach ($survey as $key => $value) {
            $survey[$key]['weight'] += floatval(($survey[$key]['budget_cost'] / $total) * 100);
            $totalWeight += $survey[$key]['weight'];
        }
        $this->getBugetCostByBuildingColumnChart($survey);
        $this->getBugetCostByBuildingPieChart($survey);
        return view('reports.budget_cost_by_discipline', compact('project', 'survey', 'total', 'totalWeight'));

    }

    public function getBugetCostByBuildingColumnChart($data)
    {
        $costTable = \Lava::DataTable();
        $costTable->addStringColumn('BudgetCost')->addNumberColumn('Discipline');
        foreach ($data as $key => $value) {
            $costTable->addRow([$key, $data[$key]['budget_cost']]);
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
    }

    public function getBugetCostByBuildingPieChart($data)
    {
        $building = \Lava::DataTable();
        $building->addStringColumn('Buildings')->addNumberColumn('Weight');
        foreach ($data as $key => $value) {
            $building->addRow([$key, $data[$key]['weight']]);
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