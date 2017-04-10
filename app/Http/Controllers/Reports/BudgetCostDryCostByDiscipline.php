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

    public function compareBudgetCostDryCostDiscipline (Project $project)
    {
        set_time_limit(300);
        $total = ['budget' => 0, 'dry' => 0, 'difference' => 0, 'increase' => 0];
        $this->project = $project;
        $this->shadow = collect();
        $this->wbs_levels = collect();

        $wbs_levels = $project->wbs_levels()->get();
        foreach ($wbs_levels as $level) {
            $treeLevel = $this->buildReport($level);
            $tree [] = $treeLevel;
        }

        collect(\DB::select(\DB::raw('SELECT std . discipline, SUM(sh . budget_cost) AS budget
                                            FROM break_down_resource_shadows sh, std_activities std
                                              WHERE sh . project_id = ' . $project->id . '
                                                    AND sh . activity_id = std . id
                                              GROUP BY std . discipline')))->map(function ($shadow) {
            $this->data[$shadow->discipline]['budget'] = $shadow->budget;
        });


        $data = $this->data;
        foreach ($data as $key => $value) {

            $data[$key]['difference'] = $data[$key]['dry'] - $data[$key]['budget'];
            if ($data[$key]['dry']) {
                $data[$key]['increase'] = $data[$key]['difference'] / $data[$key]['dry'];
                $total['budget'] += $data[$key]['budget'];
                $total['dry'] += $data[$key]['dry'];
                $total['difference'] = $total['dry'] - $total['budget'];
                $total['increase'] = $total['difference'] / $total['dry'];
            }

        }
        return view('reports.budget_cost_dry_cost_by_discipline', compact('data', 'total', 'project'));
    }

    private function buildReport (WbsLevel $level)
    {
        $boqs = Boq::where('project_id', $this->project->id)->where('wbs_id', $level->id)->get();
        $breakdowns = Breakdown::where('project_id', $this->project->id)
            ->where('wbs_level_id', $level->id)->get();

        if(count($boqs) && count($breakdowns)){
            foreach ($boqs as $boq) {
                $breakdown = Breakdown::with('std_activity')->where('project_id', $this->project->id)
                    ->where('wbs_level_id', $level->id)->where('cost_account', $boq->cost_account)->first();
                if ($breakdown) {
                    $discpline = $breakdown->std_activity->discipline;
                    if (!isset($this->data[$discpline])) {
                        $this->data[$discpline] = ['budget' => 0, 'dry' => 0, 'difference' => 0, 'increase' => 0];
                    }
                    $this->data[$discpline]['dry'] += ($boq->dry_ur * $boq->quantity);
                }
            }
        }
        if(count($boqs) && !count($breakdowns)){
            foreach ($boqs as $boq) {
                $breakdown = Breakdown::with('std_activity')->where('project_id', $this->project->id)
                    ->whereIn('wbs_level_id', $level->getChildrenIds())->where('cost_account', $boq->cost_account)->first();
                if ($breakdown) {
                    $discpline = $breakdown->std_activity->discipline;
                    if (!isset($this->data[$discpline])) {
                        $this->data[$discpline] = ['budget' => 0, 'dry' => 0, 'difference' => 0, 'increase' => 0];
                    }
                    $this->data[$discpline]['dry'] += ($boq->dry_ur * $boq->quantity);
                }
            }
        }




    }

    public function getBudgetCostDryCostColumnChart ($data)
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

    public function getBudgetCostDryCostSecondColumnChart ($data)
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