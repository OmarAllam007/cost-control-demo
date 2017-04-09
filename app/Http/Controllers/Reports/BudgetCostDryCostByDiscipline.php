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

class BudgetCostDryCostByDiscipline
{

    private $project;
    private $activities;
    private $shadow;
    private $data;
    private $total_budget;
    private $wbs_levels;

    public function compareBudgetCostDryCostDiscipline (Project $project)
    {
        $total = ['budget' => 0, 'dry' => 0, 'difference' => 0, 'increase' => 0];
        $this->project = $project;
        $this->shadow = collect();
        $this->wbs_levels = collect();
//        $this->activities = StdActivity::all()->keyBy('id')->map(function ($activity) {
//            return $activity->discipline;
//        });

//        collect(\DB::select('SELECT cost_account, SUM(budget_cost) AS sum
//FROM break_down_resource_shadows
//WHERE project_id = ' . $project->id . '
//GROUP BY cost_account'))->map(function ($shadow) {
//            $this->shadow->put($shadow->cost_account, $shadow->sum);
//        });

//        collect(\DB::select('SELECT DISTINCT wbs_id FROM break_down_resource_shadows WHERE project_id=' . $project->id))
//            ->map(function ($level) {
//                $this->wbs_levels->push($level->wbs_id);
//            });

        $ids = collect(\DB::select('SELECT
  DISTINCT
  w.id,
  w.name
FROM wbs_levels w
  JOIN boqs b
WHERE b.wbs_id = w.id and w.project_id=?',[$project->id]))->pluck('id');

        $wbs_levels = $project->wbs_levels()->whereIn('id',$ids)->get();
        
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
            if(isset($data[$key]['dry']) && $data[$key]['dry']!=0){
                $data[$key]['difference'] = $data[$key]['dry'] - $data[$key]['budget'];
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

            $breakdowns = Breakdown::where('project_id', $this->project->id)
                ->where('wbs_level_id', $level->id)->get();
            if (count($breakdowns)) {
                foreach ($breakdowns as $breakdown) {
                    $dry = \DB::select("SELECT SUM(dry_ur * quantity) AS sum FROM boqs
                                WHERE project_id= ?
                                AND wbs_id= ? AND boqs.cost_account =?", [$this->project->id, $level->id, $breakdown['cost_account']]);
                    $discpline = $breakdown->std_activity->discipline;
                    if (!isset($this->data[$discpline])) {
                        $this->data[$discpline] = ['budget' => 0, 'dry' => 0, 'difference' => 0, 'increase' => 0];
                    }
                    $this->data[$discpline]['dry'] += $dry[0]->sum;
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