<?php
/**
 * Created by PhpStorm.
 * User: omar
 * Date: 29/12/16
 * Time: 10:32 ص
 */

namespace App\Http\Controllers\Reports\CostReports;


use App\BreakDownResourceShadow;
use App\CostShadow;
use App\Project;
use App\Resources;
use App\ResourceType;

class ResourceDictionaryReport
{

    protected $project;
    protected $data = [];

    function getReport(Project $project)
    {
        $tree=[];
        $this->project = $project;
        $costShadows = CostShadow::joinBudget('budget.resource_type_id')
            ->where('budget.project_id', $project->id)->get()->pluck('resource_type_id')->unique()->toArray();

        $resourceShadows = CostShadow::joinBudget('budget.resource_id')
            ->where('budget.project_id', $project->id)->get()->pluck('resource_id')->toArray();


        $types = ResourceType::whereIn('id', $costShadows)->get();
        foreach ($types as $type) {
            $type = $this->buildTreeResources($type, $resourceShadows);
            $tree[] = $type;
        }
        return view('reports.cost-control.dictionary.resource_dictionary', compact('tree', 'project'));
    }


    function buildTreeResources(ResourceType $division, $resourceShadows)
    {
        $tree = ['id' => $division->id, 'name' => $division->name, 'children' => [], 'resources' => [], 'data' => ['to_date_cost' => 0, 'previous_cost' => 0, 'allowable_ev_cost' => 0, 'remaining_cost'
        => 0, 'completion_cost' => 0, 'cost_var' => 0, 'allowable_var' => 0, 'budget_cost' => 0]];


        if ($division->resources->count()) {
            $tree['resources'] = $division->resources->whereIn('id', $resourceShadows)->map(function (Resources $resource) {
                $costData = CostShadow::sumColumns(['to_date_cost', 'previous_cost', 'allowable_ev_cost', 'remaining_cost'
                    , 'completion_cost', 'cost_var', 'allowable_var'])
                    ->where('project_id', $this->project->id)->where('resource_id', $resource->id)->get()->toArray()[0];

                if (!isset($costData['budget_cost'])) {
                    $costData['budget_cost'] = BreakDownResourceShadow::where('project_id', $this->project->id)
                        ->where('resource_id', $resource->id)->get()->sum('budget_cost');
                }
                return ['id' => $resource->id, 'code' => $resource->resource_code, 'name' => $resource->name, 'data' => $costData];
            });
        }


        foreach ($tree['resources'] as $resource) {
            $tree['data']['budget_cost'] = BreakDownResourceShadow::whereIn('resource_type_id',$division->getChildrenIds())->where('project_id',$this->project->id)->get()->sum('budget_cost');
            $tree['data']['to_date_cost'] += $resource['data']['to_date_cost'];
            $tree['data']['previous_cost'] += $resource['data']['previous_cost'];
            $tree['data']['allowable_ev_cost'] += $resource['data']['allowable_ev_cost'];
            $tree['data']['remaining_cost'] += $resource['data']['remaining_cost'];
            $tree['data']['completion_cost'] += $resource['data']['completion_cost'];
            $tree['data']['cost_var'] += $resource['data']['cost_var'];
            $tree['data']['allowable_var'] += $resource['data']['allowable_var'];
        }
        if ($division->children->count()) {
            $tree['children'] = $division->children->map(function (ResourceType $child) use ($resourceShadows, $tree) {

                return $this->buildTreeResources($child, $resourceShadows);
            });
        }


        return $tree;
    }
}
