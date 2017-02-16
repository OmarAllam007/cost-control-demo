<?php
/**
 * Created by PhpStorm.
 * User: omar.garana
 * Date: 10/11/2016
 * Time: 2:53 PM
 */

namespace App\Http\Controllers\Reports;


use App\Boq;
use App\BreakDownResourceShadow;
use App\Project;
use App\Survey;
use App\WbsLevel;

class BoqPriceList
{
    private $boqs;
    private $survies;
    private $project;
    private $shadows;
    private $boq_equavalent_rate;

    public function getBoqPriceList(Project $project)
    {
        set_time_limit(300);
        $this->project = $project;
        $this->survies = collect();
        $this->boqs = collect();
        $this->boq_equavalent_rate = collect();
        $wbs_levels = WbsLevel::where('project_id', $project->id)->with('children', 'children.children', 'children.children.children')->tree()->get();
        collect(\DB::select('SELECT b.cost_account , b.description
                            FROM boqs b
                            WHERE b.project_id=' . $project->id . '
                            GROUP BY b.cost_account'))->map(function ($boq) {
            $this->boqs->put($boq->cost_account, $boq->description);
        });
        collect(\DB::select('SELECT cost_account , u.type
                                      FROM qty_surveys qs, units u
                                      WHERE qs.project_id = ' . $project->id . ' AND u.id = qs.unit_id
                                      GROUP BY cost_account'))->map(function ($survey) {
            $this->survies->put($survey->cost_account, $survey->type);
        });

        collect(\DB::select('SELECT wbs_id, boq_equivilant_rate
                              FROM break_down_resource_shadows
                              WHERE project_id = ' . $project->id . '
                              GROUP BY wbs_id'))->map(function ($shadow) {
            $this->boq_equavalent_rate->put($shadow->wbs_id, $shadow->boq_equivilant_rate);
        });

        $tree = [];
        foreach ($wbs_levels as $level) {
            $treeLevel = $this->getReportTree($level);
            $tree[] = $treeLevel;
        }
        return view('reports.budget.boq_price_list.boq_price_list', compact('project', 'tree'));
    }

    private function getReportTree($level)
    {

        $tree = ['id' => $level->id
            , 'code' => $level->code
            , 'name' => $level->name
            , 'children' => []
            , 'boqs' => []
            , 'level_boq_equavalent_rate' => 0];

        $shadows = BreakDownResourceShadow::where('project_id', $this->project->id)->where('wbs_id', $level->id)->get();
        foreach ($shadows as $shadow) {
            $cost_account = $shadow['cost_account'];
            $boq = $this->boqs->get($shadow['cost_account']);
            if (!isset($tree['boqs'][$boq]['items'][$cost_account])) {
                $tree['boqs'][$boq]['items'][$cost_account] = [
                    'cost_account' => $cost_account,
                    'unit' => $this->survies->get($shadow['cost_account']),
                    'GEN' => 0,
                    'LAB' => 0,
                    'MAT' => 0,
                    'SUB' => 0,
                    'EQU' => 0,
                    'SCA' => 0,
                    'OTH' => 0,
                    'total_resources' => 0,
                ];




            }
            $name = mb_strtoupper(substr($shadow['resource_type'], strpos($shadow['resource_type'], '.') + 1, 3));
            if (isset($tree['boqs'][$boq]['items'][$cost_account][$name])) {
                $tree['boqs'][$boq]['items'][$cost_account][$name] += $shadow['boq_equivilant_rate'];
                $tree['boqs'][$boq]['items'][$cost_account]['total_resources'] += $shadow['boq_equivilant_rate'];

            }
        };


        /** @var WbsLevel $level */
        $tree['level_boq_equavalent_rate'] += BreakDownResourceShadow::where('project_id', $this->project->id)
            ->whereIn('wbs_id', $level->getChildrenIds())->sum('boq_equivilant_rate');

        if ($level->children && $level->children->count()) {
            $tree['children'] = $level->children->map(function (WbsLevel $childLevel) {
                return $this->getReportTree($childLevel);
            });
        }
        return $tree;
    }


}

