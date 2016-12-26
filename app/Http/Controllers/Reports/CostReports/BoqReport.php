<?php
/**
 * Created by PhpStorm.
 * User: omar
 * Date: 24/12/16
 * Time: 07:39 م
 */

namespace App\Http\Controllers\Reports\CostReports;


use App\Breakdown;
use App\BreakDownResourceShadow;
use App\CostShadow;
use App\Project;
use App\WbsLevel;

class BoqReport
{
    protected $root_ids = [];

    public $project;

    function getReport(Project $project)
    {
        $this->project = $project;
        $tree = [];
        $levels = CostShadow::all()->pluck('wbs_level_id')->unique()->toArray();
        $wbs_levels = WbsLevel::whereIn('id', $levels)->where('project_id', $project->id)->get();
        foreach ($wbs_levels as $level) {
            if ($level->root) {
                $treeLevel = $this->buildTree($level->root, $levels);
            } else {
                $treeLevel = $this->buildTree($level, $levels);
            }

            if ($treeLevel) {
                $tree[] = $treeLevel;
            }
        }

        return view('reports.cost-control.boq-report.boq_report', compact('tree', 'levels', 'project'));
    }

    function buildTree($level, $levels)
    {
        $tree = [];
        if (!in_array($level->id, $this->root_ids)) {
            $this->root_ids[] = $level->id;
            $tree = ['id' => $level->id, 'name' => $level->name, 'children' => [], 'data' => [
                'to_date_cost' => 0,
                'previous_cost' => 0,
                'allowable_ev_cost' => 0,
                'remaining_cost' => 0,
                'completion_cost' => 0,
                'cost_var' => 0,
                'allowable_var'=>0,
                'budget_cost' => 0,
            ]];

            $shadows = CostShadow::sumColumns([
                'to_date_cost',
                'previous_cost',
                'allowable_ev_cost',
                'remaining_cost',
                'completion_cost',
                'cost_var',
                'allowable_var'])
                ->where('period_id', $this->project->open_period()->id)
                ->whereIn('wbs_level_id', $level->getChildrenIds())
                ->get()->toArray();


            $budget_cost = BreakDownResourceShadow::where('project_id', $this->project->id)
                ->whereIn('wbs_id', $level->getChildrenIds())->get()->sum('budget_cost');

            $tree['data']['budget_cost'] = $budget_cost;

            foreach ($shadows as $shadow) {

                $tree['data']['to_date_cost'] = $shadow['to_date_cost'] ?:0;
                $tree['data']['previous_cost'] = $shadow['previous_cost'] ?: 0;
                $tree['data']['allowable_ev_cost'] = $shadow['allowable_ev_cost'] ?:0 ;
                $tree['data']['remaining_cost'] = $shadow['remaining_cost'] ?: 0;
                $tree['data']['completion_cost'] = $shadow['completion_cost'] ?: 0;
                $tree['data']['cost_var'] = $shadow['cost_var'] ?: 0;
                $tree['data']['allowable_var'] = $shadow['allowable_var'] ?: 0;

            }

            if ($level->children->count()) {
                $tree['children'] = $level->children->map(function (WbsLevel $childLevel) use ($levels, $budget_cost, $tree) {
                    return $this->buildTree($childLevel, $levels);
                });
            }
        }
        return $tree;
    }
}