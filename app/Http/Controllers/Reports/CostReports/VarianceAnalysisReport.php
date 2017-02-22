<?php
/**
 * Created by PhpStorm.
 * User: omar
 * Date: 09/01/17
 * Time: 09:23 ص
 */

namespace App\Http\Controllers\Reports\CostReports;


use App\Boq;
use App\BreakDownResourceShadow;
use App\CostShadow;
use App\Project;
use App\ResourceType;
use App\Survey;
use Illuminate\Database\Eloquent\Collection;

class VarianceAnalysisReport
{

    private $project;
    private $types;
    private $resources;
    private $discplines;
    private $resources_data;
    private $period_id;
    private $cost_account;

    function getVarianceReport(Project $project, $period_id = 1)
    {
        $this->project = $project;
        $this->period_id = $period_id;
        $this->discplines = collect();
        $this->resources_data = collect();
        collect(\DB::select('SELECT
  cost_account,description,
  type
FROM boqs
WHERE project_id = ?', [$project->id]))->map(function ($boq) {
            $this->discplines->put($boq->cost_account, $boq->type);
        });
        $this->getResourcesData()->map(function ($resource) {
            $this->resources_data->put($resource->resource_id, [
                'resource_name' => $resource->resource_name,
                'budget_unit' => $resource->budget_unit ?? 0 ,
                'unit_price' => $resource->unit_price ?? 0 ,
                'budget_cost' => $resource->budget_cost ?? 0 ,
                'curr_unit_price' => $resource->curr_unit_price ?? 0 ,
                'to_date_unit_price' => $resource->to_date_unit_price ?? 0 ,
                'unit_price_var' => $resource->unit_price_var ?? 0 ,
                'cost_variance_to_date_due_unit_price' => $resource->cost_variance_to_date_due_unit_price ?? 0 ,
                'cost_variance_completion_due_unit_price' => $resource->cost_variance_completion_due_unit_price ?? 0 ,
                'to_date_qty' => $resource->to_date_qty ?? 0 ,
                'allowable_qty' => $resource->allowable_qty ?? 0 ,
                'qty_var' => $resource->qty_var ?? 0 ,
                'cost_variance_to_date_due_qty' => $resource->cost_variance_to_date_due_qty ?? 0 ,
                'cost_variance_completion_due_qty' => $resource->cost_variance_completion_due_qty ?? 0 ,
            ]);
        });

        $types = \Cache::has('resources-tree') ? \Cache::get('resources-tree') : ResourceType::tree()->get();
        $this->types = ResourceType::whereHas('resources', function ($q) {
            $q->where('project_id', $this->project->id);
        })->get()->keyBy('id')->map(function ($type) {
            return $type->resources->where('project_id', $this->project->id);
        });
        $tree = [];
        foreach ($types as $type) {
            $treeType = $this->buildTypeTree($type);
            $tree[] = $treeType;
        }
        return view('reports.cost-control.variance_analysis.variance_analysis', compact('tree', 'project'));
    }

    function buildTypeTree($type)
    {
        $tree = ['id' => $type['id'], 'name' => $type['name'], 'children' => [], 'discpline' => [

        ], 'budget_unit' => 0,
            'unit_price' => 0,
            'budget_cost' => 0,
            'curr_unit_price' => 0,
            'to_date_unit_price' => 0,
            'unit_price_var' => 0,
            'cost_variance_to_date_due_unit_price' => 0,
            'cost_variance_completion_due_unit_price' => 0,
            'to_date_qty' => 0,
            'allowable_qty' => 0,
            'qty_var' => 0,
            'cost_variance_to_date_due_qty' => 0,
            'cost_variance_completion_due_qty' => 0,];

        $resources = $this->types->get($type['id']);
        if (count($resources)) {
            foreach ($resources as $resource) {
                $cost_accounts = \DB::select('SELECT cost_account  FROM break_down_resource_shadows 
WHERE project_id=? AND resource_id=?', [$this->project->id, $resource->id]);

                foreach ($cost_accounts as $cost_account) {
                    $cost_account_resource = $this->discplines->get($cost_account->cost_account);
                    if (!isset($tree['discpline'][$cost_account_resource])) {
                        $tree['discpline'][$cost_account_resource] = [
                            'budget_unit' => 0,
                            'unit_price' => 0,
                            'budget_cost' => 0,
                            'curr_unit_price' => 0,
                            'to_date_unit_price' => 0,
                            'unit_price_var' => 0,
                            'cost_variance_to_date_due_unit_price' => 0,
                            'cost_variance_completion_due_unit_price' => 0,
                            'to_date_qty' => 0,
                            'allowable_qty' => 0,
                            'qty_var' => 0,
                            'cost_variance_to_date_due_qty' => 0,
                            'cost_variance_completion_due_qty' => 0,
                            'resources' => [],
                        ];
                    }
                    if (!isset($tree['discpline'][$cost_account_resource]['resources'][$resource->id])) {
                        $tree['discpline'][$cost_account_resource]['resources'][$resource->id] = $this->resources_data->get($resource->id);
                    }
                    $tree['discpline'][$cost_account_resource]['unit_price'] += $tree['discpline'][$cost_account_resource]['resources'][$resource->id]['unit_price'];
                    $tree['discpline'][$cost_account_resource]['curr_unit_price'] += $tree['discpline'][$cost_account_resource]['resources'][$resource->id]['curr_unit_price'];
                    $tree['discpline'][$cost_account_resource]['to_date_unit_price'] += $tree['discpline'][$cost_account_resource]['resources'][$resource->id]['to_date_unit_price'];
                    $tree['discpline'][$cost_account_resource]['unit_price_var'] += $tree['discpline'][$cost_account_resource]['resources'][$resource->id]['unit_price_var'];
                    $tree['discpline'][$cost_account_resource]['cost_variance_to_date_due_unit_price'] += $tree['discpline'][$cost_account_resource]['resources'][$resource->id]['cost_variance_to_date_due_unit_price'];
                    $tree['discpline'][$cost_account_resource]['cost_variance_completion_due_unit_price'] += $tree['discpline'][$cost_account_resource]['resources'][$resource->id]['cost_variance_completion_due_unit_price'];
                    $tree['discpline'][$cost_account_resource]['budget_cost'] += $tree['discpline'][$cost_account_resource]['resources'][$resource->id]['budget_cost'];
                    $tree['discpline'][$cost_account_resource]['to_date_qty'] += $tree['discpline'][$cost_account_resource]['resources'][$resource->id]['to_date_qty'];
                    $tree['discpline'][$cost_account_resource]['allowable_qty'] += $tree['discpline'][$cost_account_resource]['resources'][$resource->id]['allowable_qty'];
                    $tree['discpline'][$cost_account_resource]['qty_var'] += $tree['discpline'][$cost_account_resource]['resources'][$resource->id]['qty_var'];
                    $tree['discpline'][$cost_account_resource]['cost_variance_to_date_due_qty'] += $tree['discpline'][$cost_account_resource]['resources'][$resource->id]['cost_variance_to_date_due_qty'];
                    $tree['discpline'][$cost_account_resource]['cost_variance_completion_due_qty'] += $tree['discpline'][$cost_account_resource]['resources'][$resource->id]['cost_variance_completion_due_qty'];

                    $tree['budget_cost'] += $this->resources_data->get($resource->id)['budget_cost'] ;
                    $tree['unit_price'] += $this->resources_data->get($resource->id)['unit_price'];
                    $tree['to_date_unit_price'] += $this->resources_data->get($resource->id)['to_date_unit_price'];
                    $tree['unit_price_var'] += $this->resources_data->get($resource->id)['unit_price_var'];
                    $tree['cost_variance_to_date_due_unit_price'] += $this->resources_data->get($resource->id)['cost_variance_to_date_due_unit_price'];
                    $tree['cost_variance_completion_due_unit_price'] += $this->resources_data->get($resource->id)['cost_variance_completion_due_unit_price'];
                    $tree['to_date_qty'] += $this->resources_data->get($resource->id)['to_date_qty'];
                    $tree['allowable_qty'] += $this->resources_data->get($resource->id)['allowable_qty'];
                    $tree['qty_var'] += $this->resources_data->get($resource->id)['qty_var'];
                    $tree['cost_variance_to_date_due_qty'] += $this->resources_data->get($resource->id)['cost_variance_to_date_due_qty'];
                    $tree['cost_variance_completion_due_qty'] += $this->resources_data->get($resource->id)['cost_variance_completion_due_qty'];
                }

            }
        }

//        $tree['resources'] = collect($tree['resources'])->sortBy('code');

        if (collect($type['children'])->count()) {
            $tree['children'] = collect($type['children'])->map(function ($child) use ($tree) {
                $subtree = $this->buildTypeTree($child);
                return $subtree;
            });

            foreach ($tree['children'] as $child) {
                $tree['budget_cost'] += $child['budget_cost'];
            }

        }
        return $tree;
    }

    function getResourcesData()
    {
        $query = collect(\DB::select('SELECT sh.resource_id,sh.resource_name, sum(budget_unit) budget_unit,
 sum(unit_price) unit_price, sum(budget_cost) budget_cost, sum(curr_unit_price) curr_unit_price, sum(to_date_unit_price) to_date_unit_price, sum(unit_price_var) unit_price_var, sum(cost_variance_to_date_due_unit_price) cost_variance_to_date_due_unit_price, sum(cost_variance_completion_due_unit_price) cost_variance_completion_due_unit_price, sum(to_date_qty) to_date_qty, sum(allowable_qty) allowable_qty, sum((allowable_qty-to_date_qty)) AS qty_var , sum(cost_variance_to_date_due_qty) cost_variance_to_date_due_qty, sum(cost_variance_completion_due_qty) cost_variance_completion_due_qty
FROM break_down_resource_shadows sh JOIN cost_shadows c ON sh.breakdown_resource_id = c.breakdown_resource_id
WHERE sh.project_id = ? AND c.period_id=?
GROUP BY resource_id', [$this->project->id, $this->period_id]));
        return $query;
    }

}