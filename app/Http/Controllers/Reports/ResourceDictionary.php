<?php
/**
 * Created by PhpStorm.
 * User: omar.garana
 * Date: 10/10/2016
 * Time: 4:25 PM
 */

namespace App\Http\Controllers\Reports;

use App\BreakDownResourceShadow;
use App\BusinessPartner;
use App\Project;
use App\Resources;
use App\ResourceType;
use App\Unit;
use Make\Makers\Resource;

class ResourceDictionary
{
    private $resources;
    private $units;
    private $partners;
    private $project;
    private $types;
    private $parent_cost;

    public function getResourceDictionary(Project $project)
    {
        set_time_limit(300);
        $this->project = $project;
        $this->resources = collect();
        $this->types = collect();
        $tree = [];
        $resources = \DB::select('SELECT  resource_id,measure_unit, SUM(budget_cost) AS budget_cost,sum(budget_unit) AS budget_unit 
FROM break_down_resource_shadows
WHERE project_id = ' . $project->id . '
GROUP BY resource_id , measure_unit');

        foreach ($resources as $resource) {
            $this->resources->put($resource->resource_id, ['unit' => $resource->measure_unit, 'budget_unit' => $resource->budget_unit, 'budget_cost' => $resource->budget_cost]);
        }

        $this->partners = BusinessPartner::all()->keyBy('id')->map(function ($partner) {
            return $partner->name;
        });

        $this->types = ResourceType::whereHas('resources', function ($q) {
            $q->where('project_id', $this->project->id);
        })->get()->keyBy('id')->map(function ($type) {
            return $type->resources->where('project_id', $this->project->id);
        });


        $types = ResourceType::tree()->get();
        foreach ($types as $type) {
            $treeType = $this->buildTypeTree($type);
            $tree[] = $treeType;
        }


        return view('reports.budget.resource_dictionary.resource_dictionary', compact('project', 'tree'));
    }

    protected function buildTypeTree(ResourceType $type)
    {
        $tree = ['id' => $type->id, 'name' => $type->name, 'children' => [], 'resources' => [], 'budget_cost' => 0];

        $resources = $this->types->get($type->id);
        if (count($resources)) {
            foreach ($resources as $resource) {
                $tree['resources'][$resource->id] = ['id' => $resource->id
                    , 'code' => $resource->resource_code
                    , 'name' => $resource->name
                    , 'unit' => $this->resources->get($resource->id)['unit']
                    , 'partner' => $this->partners->get($resource->business_partner_id)
                    , 'waste' => $resource->waste
                    , 'reference' => $resource->reference
                    , 'rate' => $resource->rate
                    , 'budget_cost' => $this->resources->get($resource->id)['budget_cost']
                    , 'budget_unit' => $this->resources->get($resource->id)['budget_unit']
                ];
                $tree['budget_cost'] += $this->resources->get($resource->id)['budget_cost'];

            }
        }

        $tree['resources'] = collect($tree['resources'])->sortBy('code');

        if ($type->children->count()) {
            $tree['children'] = $type->children->map(function (ResourceType $child) use ($tree) {
                $subtree = $this->buildTypeTree($child);
                return $subtree;
            });

            foreach ($tree['children'] as $child) {
                $tree['budget_cost'] += $child['budget_cost'];

            }

        }

        return $tree;
    }
}