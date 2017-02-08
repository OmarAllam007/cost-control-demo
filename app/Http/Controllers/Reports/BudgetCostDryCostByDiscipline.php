<?php
/**
 * Created by PhpStorm.
 * User: omar.garana
 * Date: 10/9/2016
 * Time: 9:00 PM
 */

namespace App\Http\Controllers\Reports;


use App\Boq;
use App\BreakDownResourceShadow;
use App\Project;
use App\Survey;

class BudgetCostDryCostByDiscipline
{

    public function compareBudgetCostDryCostDiscipline(Project $project)
    {
        set_time_limit(300);

        $total = BreakDownResourceShadow::whereProjectId($project->id)->sum('budget_cost');
        $data = [];
        $total = [
            'dry' => 0,
            'difference' => 0,
            'increase' => 0,
            'budget' => $total
        ];

        $total_dry = \DB::select(\DB::raw('SELECT std.discipline, SUM(b.quantity * b.dry_ur) AS `dry` FROM breakdowns sh, std_activities std, boqs b
                                  WHERE sh.project_id = ' . $project->id . ' AND b.project_id = sh.project_id AND sh.cost_account = b.cost_account
                                  AND sh.wbs_level_id = b.wbs_id
                                  AND sh.std_activity_id = std.id 
                                  GROUP BY std.discipline'));

        $total_budget = \DB::select(\DB::raw('SELECT std.discipline, SUM(sh.budget_cost) as budget 
                                            FROM break_down_resource_shadows sh, std_activities std 
                                              WHERE sh.project_id =' . $project->id . '
                                              AND sh.activity_id = std.id
                                              GROUP BY std.discipline'));

        foreach ($total_dry as $key => $item) {
            if (!isset($data[$item->discipline])) {
                $data[$item->discipline]['dry'] = $item->dry;
                $data[$item->discipline]['cost'] = $total_budget[$key]->budget;
                $data[$item->discipline]['difference'] = $total_budget[$key]->budget - $item->dry;
                $data[$item->discipline]['increase'] = ($data[$item->discipline]['difference'] / $item->dry) * 100;
                $total['dry'] += $data[$item->discipline]['dry'];
                $total['difference'] += $data[$item->discipline]['difference'];
                $total['increase'] += $total['difference'] / $total['dry'];

            }
        }
        $this->getBudgetCostDryCostColumnChart($data);
        $this->getBudgetCostDryCostSecondColumnChart($data);
        return view('reports.budget_cost_dry_cost_by_discipline', compact('data', 'total', 'project'));
    }

    public function getBudgetCostDryCostColumnChart($data)
    {
        $costTable = \Lava::DataTable();

        $costTable->addStringColumn('Budget Cost')->addNumberColumn('Dry Cost')->addNumberColumn('Budget Cost');
        foreach ($data as $key => $value) {
            $costTable->addRow([$key, $data[$key]['dry'], $data[$key]['cost']]);

        }
        $options = [
            'toolTip' => 'value',
            'titleTextStyle' => [
                'color' => '#eb6b2c',
                'fontSize' => 14,
                'width' => '1000',
                'height' => '600',
            ],
            'title' => trans('Budget Cost VS Dry Cost'),
            'height' => 400,
            'hAxis' => [
                'title' => 'Discipline',
            ],
            'vAxis' => [
                'title' => '',
            ],

        ];
        \Lava::ColumnChart('BudgetCost', $costTable, $options);

    }

    public function getBudgetCostDryCostSecondColumnChart($data)
    {
        $costTable = \Lava::DataTable();

        $costTable->addStringColumn('Difference')->addNumberColumn('Dry Cost');
        foreach ($data as $key => $value) {
            $costTable->addRow([$key, $data[$key]['difference']]);
        }
        $options = [
            'toolTip' => 'value',
            'titleTextStyle' => [
                'color' => '#eb6b2c',
                'fontSize' => 14,
                'width' => '1000',
                'height' => '600',
            ],
            'title' => trans('Budget Cost VS Dry Cost'),
            'height' => 400,
            'hAxis' => [
                'title' => 'Discipline',
            ],
            'vAxis' => [
                'title' => '',
            ],

        ];
        \Lava::ColumnChart('Difference', $costTable, $options);

    }

}