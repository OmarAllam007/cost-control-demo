<?php
/**
 * Created by PhpStorm.
 * User: omar
 * Date: 26/12/16
 * Time: 11:51 ص
 */

namespace App\Http\Controllers\Reports\CostReports;


use App\BreakDownResourceShadow;
use App\CostShadow;
use App\Project;
use App\Resources;
use App\ResourceType;

class ResourceCodeReport
{
    protected $project;

    public function getResourceCodeReport(Project $project)
    {
        // get resources
        // get types of resources
        // build tree

        $this->project = $project;
        $tree = [];
        $type_level = '';
        $resources = CostShadow::joinBudget('budget.resource_id')
            ->where('cost.project_id', $project->id)
            ->where('cost.period_id', $project->open_period()->id)
            ->pluck('resource_id')->unique()->toArray();

        $type_ids = Resources::whereIn('id', $resources)->pluck('resource_type_id')->unique()->toArray();
        $types = ResourceType::whereIn('id', $type_ids)->get();
        foreach ($types as $type) {
            $type_level = $this->buildTree($type->root);
        }
        if ($type_level) {
            $tree[] = $type_level;
        }
        return view('reports.cost-control.resource-code.resource_code', compact('project', 'tree'));
    }

    function buildTree($resource_type)
    {

        $tree = ['id' => $resource_type->id, 'name' => $resource_type->name, 'children' => [], 'data' => [
            'to_date_cost' => 0,
            'previous_cost' => 0,
            'allowable_ev_cost' => 0,
            'remaining_cost' => 0,
            'completion_cost' => 0,
            'cost_var' => 0,
            'allowable_var' => 0,
            'budget_cost' => 0,
        ]];

        $shadows = CostShadow::joinBudget('budget.resource_type')->sumFields([
            'cost.to_date_cost',
            'cost.previous_cost',
            'cost.allowable_ev_cost',
            'cost.remaining_cost',
            'cost.completion_cost',
            'cost.cost_var',
            'cost.allowable_var'])
            ->where('cost.period_id', $this->project->open_period()->id)
            ->whereIn('budget.resource_type_id', $resource_type->getChildrenIds())
            ->get()->toArray();

        $resources = Resources::where('project_id',$this->project->id)->whereIn('resource_type_id', $resource_type->getChildrenIds())->pluck('id')->unique()->toArray();
        $budget_cost = BreakDownResourceShadow::where('project_id', $this->project->id)
            ->whereIn('resource_id', $resources)->get()->sum('budget_cost');

        $tree['data']['budget_cost'] = $budget_cost;

        foreach ($shadows as $shadow) {
            $tree['data']['to_date_cost'] = $shadow['to_date_cost'] ?: 0;
            $tree['data']['previous_cost'] = $shadow['previous_cost'] ?: 0;
            $tree['data']['allowable_ev_cost'] = $shadow['allowable_ev_cost'] ?: 0;
            $tree['data']['remaining_cost'] = $shadow['remaining_cost'] ?: 0;
            $tree['data']['completion_cost'] = $shadow['completion_cost'] ?: 0;
            $tree['data']['cost_var'] = $shadow['cost_var'] ?: 0;
            $tree['data']['allowable_var'] = $shadow['allowable_var'] ?: 0;
        }

        if ($resource_type->children->count()) {
            $tree['children'] = $resource_type->children->map(function (ResourceType $type) {
                return $this->buildTree($type);
            });
        }

        return $tree;
    }

}