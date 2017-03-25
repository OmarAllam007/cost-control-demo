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
    private $dry;
    private $project;
    private $total;

    public function getRevised ($project)
    {
        $this->boqs = collect();
        $this->survies = collect();
        $this->activities = collect();
        $this->shadows = collect();
        $this->dry = collect();
        $this->project = $project;
        $this->total = ['revised' => 0, 'original' => 0];
        set_time_limit(300);

        collect(\DB::select('SELECT
  br.id breakdown_id,
  activity.id AS activity_id,
  activity.name
FROM breakdowns br JOIN std_activities activity ON br.std_activity_id = activity.id
WHERE project_id =?', [$project->id]))->map(function ($breakdown) {
            $this->activities->put($breakdown->breakdown_id, ['activity_id' => $breakdown->activity_id, 'activity_name' => $breakdown->name]);
        });

        collect(\DB::select('SELECT wbs_id , cost_account,price_ur , quantity, description FROM boqs
WHERE project_id=?', [$project->id]))->map(function ($boq) {
            $this->boqs->put($boq->wbs_id . $boq->cost_account, ['price' => $boq->price_ur, 'quantity' => $boq->quantity, 'description' => $boq
                ->description]);
        });

        collect(\DB::select('SELECT wbs_level_id , cost_account , eng_qty FROM qty_surveys
WHERE project_id=?', [$project->id]))->map(function ($survey) {
            $this->survies->put($survey->wbs_level_id . $survey->cost_account, $survey->eng_qty);
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
//        $tree = collect($tree)->sortBy('name');
        $total = $this->total;
        return view('reports.budget.revised_boq.revised_boq', compact('project', 'tree', 'total'));

    }

    private function buildReport (WbsLevel $level)
    {
        $tree = ['id' => $level->id, 'code' => $level->code, 'children' => [], 'name' => $level->name, 'activities' => [], 'revised_boq' => 0, 'original_boq' => 0];

        $boq_data = \DB::select('SELECT
  wbs_id,
  sum(price_ur * quantity) AS original,
  sum(price_ur * (SELECT eng_qty FROM qty_surveys WHERE qty_surveys.project_id=? AND wbs_level_id=? AND qty_surveys.cost_account = boqs.cost_account AND qty_surveys.wbs_level_id = boqs.wbs_id) )  AS revised
FROM boqs
WHERE project_id = ? AND wbs_id = ?', [$this->project->id, $level->id, $this->project->id, $level->id]);

        if (isset($boq_data[0]) && $boq_data[0]->revised!=null) {
            $tree['revised_boq'] = $boq_data[0]->revised ?? 0;
            $tree['original_boq'] = $boq_data[0]->original ?? 0;
        } else {
            foreach ($this->breakdowns->get($level->id) as $breakdown) {
                $boq = $this->boqs->get($level->id . $breakdown->cost_account);
                $survey = $this->survies->get($level->id . $breakdown->cost_account);
                $activity = $this->activities->get($breakdown->id);

                if (!isset($tree['activities'][$activity['activity_id']])) {
                    $tree['activities'][$activity['activity_id']] = ['name' => $activity['activity_name'], 'revised_boq' => 0, 'original_boq' => 0, 'cost_accounts' => []];
                }

                if (!$boq && !$survey) {
                    $parent = $level;
                    while ($parent->parent) {
                        $parent = $parent->parent;
                        $boq = $this->boqs->get($parent->id . $breakdown->cost_account);
                        $survey = $this->survies->get($parent->id . $breakdown->cost_account);
                        if ($boq && $survey) {
                            break;
                        }
                    }
                }

                if (!isset($tree['activities'][$activity['activity_id']]['cost_accounts'][$breakdown->cost_account])) {
                    $tree['activities'][$activity['activity_id']]['cost_accounts'][$breakdown->cost_account] =
                        ['cost_account' => $breakdown->cost_account, 'description' => $boq['description'],
                            'revised_boq' => $boq['price'] * $survey, 'original_boq' => $boq['price'] * $boq['quantity']];

                }

                $tree['activities'][$activity['activity_id']]['revised_boq'] += $tree['activities'][$activity['activity_id']]['cost_accounts'][$breakdown->cost_account]['revised_boq'];
                $tree['activities'][$activity['activity_id']]['original_boq'] += $tree['activities'][$activity['activity_id']]['cost_accounts'][$breakdown->cost_account]['original_boq'];
                $tree['revised_boq'] += $tree['activities'][$activity['activity_id']]['cost_accounts'][$breakdown->cost_account]['revised_boq'];
                $tree['original_boq'] += $tree['activities'][$activity['activity_id']]['cost_accounts'][$breakdown->cost_account]['original_boq'];

            }
        }


        $boq_exist = Boq::where('project_id', $this->project->id)->where('wbs_id', $level->id)->where('dry_ur', '<>', 0)->first();
        if ($boq_exist) {
            $this->total['revised'] += $tree['revised_boq'];
            $this->total['original'] += $tree['original_boq'];
        }
        if ($level->children->count()) {
            $tree['children'] = $level->children->map(function (WbsLevel $childLevel) {
                return $this->buildReport($childLevel);
            });

            foreach ($tree['children'] as $child) {
                $boq = Boq::where('project_id', $this->project->id)->where('wbs_id', $child['id'])->first();
                if ($boq && $boq->dry_ur) {
                    $tree['revised_boq'] += $child['revised_boq'];
                    $tree['original_boq'] += $child['original_boq'];
                }

            }

        }

        return $tree;
    }
}