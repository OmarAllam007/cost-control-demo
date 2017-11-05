<?php

namespace App\Reports\Cost;

use App\MasterShadow;
use App\Period;
use App\ResourceType;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Collection;

class WasteIndexReport
{
    /** @var Period */
    private $period;

    /** @var Project */
    private $project;

    /** @var Collection */
    private $types;

    /** @var Collection */
    private $resources;

    /** @var Collection */
    private $tree;

    public function __construct(Period $period)
    {
        $this->period = $period;
        $this->project = $period->project;
    }

    function run()
    {
        $this->types = ResourceType::all()->groupBy('parent_id');

        $this->resources = MasterShadow::from('master_shadows as sh')
            ->where('sh.period_id', $this->period->id)
            ->where('sh.resource_type_id', 3)
            ->join('resources as r', 'sh.resource_id', '=', 'r.id')
            ->selectRaw('sh.resource_name, r.resource_type_id, sum(sh.to_date_qty) as to_date_qty')
            ->selectRaw('sum(sh.allowable_qty) as allowable_qty, avg(sh.to_date_unit_price) as to_date_unit_price')
            ->selectRaw('sum(sh.allowable_ev_cost) as allowable_cost, sum(to_date_cost) as to_date_cost')
            ->selectRaw('sum(sh.allowable_ev_cost - to_date_cost) as to_date_cost_var')
            ->selectRaw('sum(to_date_qty_var) as qty_var, sum(pw_index) as pw_index')
            ->groupBy(['sh.resource_name', 'r.resource_type_id'])
            ->get()->groupBy('resource_type_id');

        $this->tree = $this->buildTree();

        return ['project' => $this->project, 'period' => $this->period, 'tree' => $this->tree];
    }

    private function buildTree($parent = 3)
    {
        return $this->types->get($parent, collect())->map(function($type) {
            $type->subtree = $this->buildTree($type->id);

            $type->resources_list = $this->resources->get($type->id, collect())->map(function($resource) {
                if (!$resource->pw_index && $resource->allowable_qty) {
                    // Some report builds doesn't have a PW Index field generated
                    $resource->pw_index = ($resource->allowable_qty - $resource->to_date_qty) * 100 / $resource->allowable_qty;
                } else {
                    $resource->pw_index = 0;
                }

                return $resource;
            });

            return $type;
        })->reject(function ($type) {
            return $type->subtree->isEmpty() && $type->resources_list->isEmpty();
        });
    }

    function excel()
    {

    }

}