<?php


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
    private $dry;
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

        $wbs_levels = $project->wbs_tree;
        $tree = [];
        foreach ($wbs_levels as $level) {
            $treeLevel = $this->buildReport($level);
            $tree [] = $treeLevel;
        }
        $tree = collect($tree)->sortBy('name');

        return view('reports.budget.revised_boq.revised_boq', compact('project', 'tree'));
    }

    private function buildReport(WbsLevel $level)
    {
        $tree = ['id' => $level->id, 'code' => $level->code,'children'=>[], 'name' => $level->name, 'activities' => [], 'revised_boq' => 0, 'original_boq' => 0];

        $boq = Boq::where('wbs_id',$level->id)->where('project_id',$this->project->id)->where('dry_ur','<>',0)->first();

        if ($boq) {
            foreach ($this->breakdowns->get($level->id) as $breakdown) {
                $boq = \DB::select('SELECT price_ur , quantity, description FROM boqs
WHERE project_id=?
AND wbs_id=?
AND cost_account=?', [$this->project->id, $level->id, $breakdown->cost_account]);
                $survey = \DB::select('SELECT eng_qty FROM qty_surveys
WHERE project_id=?
AND wbs_level_id=?
AND cost_account=?', [$this->project->id, $level->id, $breakdown->cost_account]);
                $activity = $this->activities->get($breakdown->id);
                if (!isset($tree['activities'][$activity['activity_id']])) {
                    $tree['activities'][$activity['activity_id']] = ['name' => $activity['activity_name'], 'revised_boq' => 0, 'original_boq' => 0, 'cost_accounts' => []];
                }
                if ($boq && $survey) {
                    if (!isset($tree['activities'][$activity['activity_id']]['cost_accounts'][$breakdown->cost_account])) {
                        $tree['activities'][$activity['activity_id']]['cost_accounts'][$breakdown->cost_account] = ['cost_account' => $breakdown->cost_account, 'description' => $boq[0]->description, 'revised_boq' => $boq[0]->price_ur * $survey[0]->eng_qty, 'original_boq' => $boq[0]->price_ur * $boq[0]->quantity];
                        $tree['activities'][$activity['activity_id']]['revised_boq'] += $tree['activities'][$activity['activity_id']]['cost_accounts'][$breakdown->cost_account]['revised_boq'];
                        $tree['activities'][$activity['activity_id']]['original_boq'] += $tree['activities'][$activity['activity_id']]['cost_accounts'][$breakdown->cost_account]['original_boq'];

                    }

                }
            }

            foreach ($tree['activities'] as $key => $activity) {
                $tree['revised_boq'] += $activity['revised_boq'];
                $tree['original_boq'] += $activity['original_boq'];
            }
        }
//         else {
//
//            $parent = $level;
//            while ($parent->parent) {
//                $parent = $parent->parent;
//                $boq = Boq::where('wbs_id',$parent->id)->where('project_id',$this->project->id)->where('dry_ur','<>',0)->first();
//                if ($boq) {
//                    foreach ($this->breakdowns->get($level->id) as $breakdown) {
//                        $boq = \DB::select('SELECT price_ur , quantity ,description FROM boqs
//WHERE project_id=?
//AND wbs_id=?
//AND cost_account=?', [$this->project->id, $parent->id, $breakdown->cost_account]);
//
//                        $survey = \DB::select('SELECT eng_qty FROM qty_surveys
//WHERE project_id=?
//AND wbs_level_id=?
//AND cost_account=?', [$this->project->id, $parent->id, $breakdown->cost_account]);
//                        $activity = $this->activities->get($breakdown->id);
//                        if (!isset($tree['activities'][$activity['activity_id']])) {
//                            $tree['activities'][$activity['activity_id']] = ['name' => $activity['activity_name'], 'revised_boq' => 0, 'original_boq' => 0, 'cost_accounts' => []];
//                        }
//                        if ($boq && $survey) {
//                            if (!isset($tree['activities'][$activity['activity_id']]['cost_accounts'][$breakdown->cost_account])) {
//                                $tree['activities'][$activity['activity_id']]['cost_accounts'][$breakdown->cost_account] = ['cost_account' => $breakdown->cost_account,'description'=>$boq[0]->description ,'revised_boq' => $boq[0]->price_ur * $survey[0]->eng_qty, 'original_boq' => $boq[0]->price_ur * $boq[0]->quantity];
//                                $tree['activities'][$activity['activity_id']]['revised_boq'] += $tree['activities'][$activity['activity_id']]['cost_accounts'][$breakdown->cost_account]['revised_boq'];
//                                $tree['activities'][$activity['activity_id']]['original_boq'] += $tree['activities'][$activity['activity_id']]['cost_accounts'][$breakdown->cost_account]['original_boq'];
//                            }
//                        }
//                    }
//                    foreach ($tree['activities'] as $key => $activity) {
//                        $tree['revised_boq'] += $activity['revised_boq'];
//                        $tree['original_boq'] += $activity['original_boq'];
//                    }
//                }
//            }
//        }

        if ($level->children->count()) {
            $tree['children'] = $level->children->map(function (WbsLevel $childLevel) {
                return $this->buildReport($childLevel);
            });

            foreach ($tree['children'] as $child){
                $tree['revised_boq']+=$child['revised_boq'];
                $tree['original_boq']+=$child['original_boq'];
            }
        }
        return $tree;
    }

}
