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
use App\BreakDownResourceShadow;
use App\Project;
use App\Survey;
use App\WbsLevel;
use Khill\Lavacharts\Lavacharts;

class RevisedBoq
{

    private $boqs;
    private $survies;
    private $breakdowns;
    private $activities;
    private $shadows;
    private $data = [];
    private $dry ;
    private $project;

    public function getRevised($project)
    {
        $this->boqs = collect();
        $this->activities = collect();
        $this->shadows = collect();
        $this->dry = collect();
        $this->project = $project;
        set_time_limit(300);

        collect(\DB::select('SELECT
  br.id breakdown_id,
  activity.id AS activity_id,
  activity.name
FROM breakdowns br JOIN std_activities activity ON br.std_activity_id = activity.id
WHERE project_id =?', [$project->id]))->map(function ($breakdown) {
            $this->activities->put($breakdown->breakdown_id, ['activity_id' => $breakdown->activity_id, 'activity_name' => $breakdown->name]);
        });



        $this->breakdowns = WbsLevel::where('project_id', $project->id)->get()->keyBy('id')->map(function ($level) {
            return $level->breakdowns;
        });
        $this->dry = Boq::where('project_id',$project->id)->get()->keyBy('wbs_id')->map(function ($boq){
           return $boq->dry_ur;
        });




        $wbs_levels = $project->wbs_levels;
        $tree = [];
        foreach ($wbs_levels as $level) {
            $treeLevel = $this->buildReport($level);
            $tree [] = $treeLevel;
        }
        return view('reports.budget.revised_boq.revised_boq', compact('project', 'tree'));
    }

    private function buildReport(WbsLevel $level)
    {

        $tree = ['id' => $level->id, 'code' => $level->code, 'name' => $level->name, 'activities' => [], 'revised_boq' => 0, 'original_boq' => 0];

        if ($this->dry->get($level->id)>0) {
            foreach ($this->breakdowns->get($level->id) as $breakdown) {
                $boq = \DB::select('SELECT price_ur , quantity from boqs
WHERE project_id=?
AND wbs_id=?
AND cost_account=?',[$this->project->id,$level->id,$breakdown->cost_account]);

                $survey = \DB::select('SELECT eng_qty from qty_surveys
WHERE project_id=?
AND wbs_level_id=?
AND cost_account=?',[$this->project->id,$level->id,$breakdown->cost_account]);

                $activity = $this->activities->get($breakdown->id);
                if (!isset($tree['activities'][$activity['activity_id']])) {
                    $tree['activities'][$activity['activity_id']] = ['name' => $activity['activity_name'], 'revised_boq' => 0, 'original_boq' => 0, 'cost_accounts' => []];
                }
                if($boq && $survey){
                    if (!isset($tree['activities'][$activity['activity_id']]['cost_accounts'][$breakdown->cost_account])) {
                        $tree['activities'][$activity['activity_id']]['cost_accounts'][$breakdown->cost_account] = ['cost_account'=>$breakdown->cost_account , 'revised_boq'=>$boq[0]->price_ur*$survey[0]->eng_qty , 'original_boq'=>$boq[0]->price_ur * $boq[0]->quantity];
                        $tree['activities'][$activity['activity_id']]['revised_boq']+=$tree['activities'][$activity['activity_id']]['cost_accounts'][$breakdown->cost_account]['revised_boq'];
                        $tree['activities'][$activity['activity_id']]['original_boq']+=$tree['activities'][$activity['activity_id']]['cost_accounts'][$breakdown->cost_account]['original_boq'];

                    }

                }
            }

            foreach ($tree['activities'] as $key=>$activity){
                $tree['revised_boq']+=$activity['revised_boq'];
                $tree['original_boq']+=$activity['original_boq'];
            }

        } else {
            $parent = $level;
            while($parent->parent){
                $parent = $parent->parent;
                if($this->dry->get($level->id)>0){
                    foreach ($this->breakdowns->get($level->id) as $breakdown) {
                        $boq = \DB::select('SELECT price_ur , quantity from boqs
WHERE project_id=?
AND wbs_id=?
AND cost_account=?',[$this->project->id,$level->id,$breakdown->cost_account]);

                        $survey = \DB::select('SELECT eng_qty from qty_surveys
WHERE project_id=?
AND wbs_level_id=?
AND cost_account=?',[$this->project->id,$level->id,$breakdown->cost_account]);
                        $activity = $this->activities->get($breakdown->id);
                        if (!isset($tree['activities'][$activity['activity_id']])) {
                            $tree['activities'][$activity['activity_id']] = ['name' => $activity['activity_name'], 'revised_boq' => 0, 'original_boq' => 0, 'cost_accounts' => []];
                        }
                        if($boq && $survey){
                            if (!isset($tree['activities'][$activity['activity_id']]['cost_accounts'][$breakdown->cost_account])) {
                                $tree['activities'][$activity['activity_id']]['cost_accounts'][$breakdown->cost_account] = ['cost_account'=>$breakdown->cost_account , 'revised_boq'=>$boq[0]->price_ur*$survey[0]->eng_qty , 'original_boq'=>$boq[0]->price_ur * $boq[0]->quantity];
                                $tree['activities'][$activity['activity_id']]['revised_boq']+=$tree['activities'][$activity['activity_id']]['cost_accounts'][$breakdown->cost_account]['revised_boq'];
                                $tree['activities'][$activity['activity_id']]['original_boq']+=$tree['activities'][$activity['activity_id']]['cost_accounts'][$breakdown->cost_account]['original_boq'];
                            }
                        }
                    }
                    foreach ($tree['activities'] as $key=>$activity){
                        $tree['revised_boq']+=$activity['revised_boq'];
                        $tree['original_boq']+=$activity['original_boq'];
                    }
                }
            }
        }

        return $tree;
    }


    public function getBudgetCostDryCostColumnChart($data)
    {
        $costTable = \Lava::DataTable();

        $costTable->addStringColumn('WBS')->addNumberColumn('Dry Cost')->addNumberColumn('Budget Cost');
        foreach ($data as $key => $value) {
            $costTable->addRow([$data[$key]['name'], $data[$key]['dry_cost'], $data[$key]['budget_cost']]);

        }
        $options = [
            'toolTip' => 'value',
            'titleTextStyle' => [
                'color' => '#eb6b2c',
                'fontSize' => 14,
                'width' => '1000',
                'height' => '600',
            ],
            'title' => trans('Budget VS Dry'),
            'height' => 400,
            'hAxis' => [
                'title' => 'WBS',
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

        $costTable->addStringColumn('WBS')->addNumberColumn('Difference');
        foreach ($data as $key => $value) {
            $costTable->addRow([$data[$key]['name'], $data[$key]['different']]);

        }
        $options = [
            'isStacked' => 'false',
            'tooltip' => 'value',
            'titleTextStyle' => [
                'color' => '#eb6b2c',
                'fontSize' => 14,
                'width' => '1000',
                'height' => '600',
            ],
            'title' => trans('Difference'),
            'height' => 400,
            'hAxis' => [
                'title' => 'WBS',
            ],
            'vAxis' => [
                'title' => '',
            ],
            'labels' => [
                'visible' => 'true',
            ],
            'legend' => [
                'position' => 'none',
            ],
            'bar' => [
                'groupWidth' => '30%',
            ],
        ];
        \Lava::ColumnChart('Difference', $costTable, $options);

    }

    public function getBudgetCostDryCostThirdColumnChart($data)
    {

        $costTable = \Lava::DataTable();

        $costTable->addStringColumn('WBS')->addNumberColumn('Increase');
        foreach ($data as $key => $value) {

            $costTable->addRow([$data[$key]['name'], $value['increase']]);
        }
        $options = [
            'isStacked' => 'false',
            'tooltip' => 'value',
            'titleTextStyle' => [
                'color' => '#eb6b2c',
                'fontSize' => 14,
                'width' => '1000',
                'height' => '600',
            ],
            'title' => trans('Increase'),
            'height' => 400,
            'hAxis' => [
                'title' => 'WBS',
            ],
            'vAxis' => [
                'title' => '',
            ],
            'labels' => [
                'visible' => 'true',
            ],
            'legend' => [
                'position' => 'none',
            ],
            'bar' => [
                'groupWidth' => '30%',
            ],
        ];
        \Lava::ColumnChart('Increase', $costTable, $options);


    }
}
