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
    private $activities;
    private $total;

    public function compareBudgetCostDiscipline(Project $project)
    {
        set_time_limit(600);
        $survey = [];
        $totalWeight = 0;
        $total = BreakDownResourceShadow::whereProjectId($project->id)->sum('budget_cost');

        $shadows = \DB::table('break_down_resource_shadows as sh')
            ->join('projects', 'sh.project_id', '=', 'projects.id')
            ->join('std_activities', 'sh.activity_id', '=', 'std_activities.id')
            ->where('sh.project_id', '=', $project->id)
            ->groupBy('std_activities.discipline')
            ->selectRaw('SUM(sh.budget_cost) as budget_cost,std_activities.discipline')->get();

        foreach ($shadows as $key => $value) {
            if (!isset($survey[$value->discipline])) {
                $survey[$value->discipline] = [
                    'budget_cost' => 0,
                    'weight' => 0,
                ];
            }
            $survey[$value->discipline]['budget_cost'] += $value->budget_cost;
            $survey[$value->discipline]['weight'] += floatval(($value->budget_cost / $total) * 100);
            $totalWeight += $survey[$value->discipline]['weight'];
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