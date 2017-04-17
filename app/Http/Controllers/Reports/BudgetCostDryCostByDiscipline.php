<?php
/**
 * Created by PhpStorm.
 * User: omar.garana
 * Date: 10/9/2016
 * Time: 9:00 PM
 */

namespace App\Http\Controllers\Reports;


use App\Boq;
use App\Breakdown;
use App\BreakDownResourceShadow;
use App\Project;
use App\StdActivity;
use App\Survey;
use App\WbsLevel;
use Beta\B;

class BudgetCostDryCostByDiscipline
{

    private $project;
    private $activities;
    private $shadow;
    private $data;
    private $total_budget;
    private $dry_Collect;
    private $wbs_levels;
    private $codes;

    public function compareBudgetCostDryCostDiscipline(Project $project)
    {
        set_time_limit(300);
        $total = ['budget' => 0, 'dry' => 0, 'difference' => 0, 'increase' => 0];
        $this->project = $project;
        $this->shadow = collect();
        $this->wbs_levels = collect();
        $this->codes = collect();
        BreakDownResourceShadow::with(['boq', 'std_activity'])->where('project_id', $project->id)->chunk(100000, function ($shadows) {
            foreach ($shadows as $shadow) {
                if (!isset($this->data[$shadow->std_activity->discipline])) {
                    $this->data[$shadow->std_activity->discipline] = ['dry' => 0, 'budget' => 0, 'difference' => 0, 'increase' => 0];
                }
                if ($shadow->boq_id != 0 && $shadow->boq_wbs_id != 0) {
                    $code = $shadow->boq_id . $shadow->boq_wbs_id;
                    if (!$this->codes->has($code)) {
                        $this->data[$shadow->std_activity->discipline]['dry'] += $shadow->boq->dry_ur * $shadow->boq->quantity;
                    }
                    $this->codes->put($code, $code);
                }
                $this->data[$shadow->std_activity->discipline]['budget'] += $shadow->budget_cost;
            }
        });
        $data = $this->data;
        foreach ($data as $key => $value) {
            $data[$key]['difference'] = $data[$key]['dry'] - $data[$key]['budget'];
            if ($data[$key]['dry']) {
                $data[$key]['increase'] = $data[$key]['difference'] / $data[$key]['dry'];
                $total['dry'] += $data[$key]['dry'];
                $total['increase'] = $total['difference'] / $total['dry'];
            }
            $total['difference'] += $data[$key]['difference'];
            $total['budget'] += $data[$key]['budget'];

        }

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