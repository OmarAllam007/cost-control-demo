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
use App\StdActivity;
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
    private $data;
    private $cost_data;

    function getVarianceReport(Project $project, $period_id)
    {
        $this->project = $project;
        $this->period_id = $period_id;
        $this->discplines = collect();
        $this->resources_data = collect();
        $this->cost_data = collect();
        $this->data = [];
        $this->discplines = StdActivity::all()->keyBy('id')->map(function ($data) {
            return $data->discipline;
        });
        collect(\DB::select('SELECT * FROM (
SELECT
  cost.resource_id,
  sum(curr_unit_price)                         curr_unit_price,
  sum(to_date_unit_price)                      to_date_unit_price,
  sum(unit_price_var)                          unit_price_var,
  sum(cost_variance_to_date_due_unit_price)    cost_variance_to_date_due_unit_price,
  sum(cost_variance_completion_due_unit_price) cost_variance_completion_due_unit_price,
  sum(to_date_qty)                             to_date_qty,
  sum(allowable_qty)                           allowable_qty,
  sum(allowable_qty - to_date_qty) AS        qty_var,
  sum(cost_variance_to_date_due_qty)           cost_variance_to_date_due_qty,
  sum(cost_variance_completion_due_qty)        cost_variance_completion_due_qty
FROM cost_shadows AS cost

  LEFT JOIN break_down_resource_shadows AS budget ON (cost.breakdown_resource_id = budget.breakdown_resource_id)
WHERE cost.project_id = ? AND cost.period_id = (SELECT max(p.period_id)
                                                 FROM cost_shadows p
                                                 WHERE p.breakdown_resource_id = cost.breakdown_resource_id AND
                                                       cost.period_id <= ?)
GROUP BY 1) AS data
GROUP BY 1;', [$project->id, $period_id]))->map(function ($costItem) {
            $this->cost_data->put($costItem->resource_id, ['curr_unit_price' => $costItem->curr_unit_price
                , 'to_date_unit_price' => $costItem->to_date_unit_price, 'unit_price_var' => $costItem->unit_price_var
                , 'cost_variance_to_date_due_unit_price' => $costItem->cost_variance_to_date_due_unit_price
                , 'cost_variance_completion_due_unit_price' => $costItem->cost_variance_completion_due_unit_price
                , 'to_date_qty' => $costItem->to_date_qty,'allowable_qty'=>$costItem->allowable_qty
                , 'qty_var' => $costItem->qty_var, 'cost_variance_to_date_due_qty' => $costItem->cost_variance_to_date_due_qty
                , 'cost_variance_completion_due_qty' => $costItem->cost_variance_completion_due_qty]);
        });

        $shadows = collect(\DB::select('SELECT activity_id ,resource_name,resource_type,resource_type_id, resource_id , budget_cost , budget_unit , boq_equivilant_rate  FROM break_down_resource_shadows 
WHERE project_id=? ', [$this->project->id]));

        foreach ($shadows as $shadow) {
            $discpline = $this->discplines->get($shadow->activity_id);
            if (!isset($this->data[$shadow->resource_type])) {
                $this->data[$shadow->resource_type] = ['id'=>$shadow->resource_type_id,'name'=>$shadow->resource_type,'disciplines' => []];
            }
            if (!isset($this->data[$shadow->resource_type]['disciplines'][$discpline])) {
                $this->data[$shadow->resource_type]['disciplines'][$discpline] = ['name'=>$discpline,'resources' => []];
            }
            if (!isset($this->data[$shadow->resource_type]['disciplines'][$discpline]['resources'][$shadow->resource_id])) {
                $this->data[$shadow->resource_type]['disciplines'][$discpline]['resources'][$shadow->resource_id] = [
                    'resource_name' => $shadow->resource_name,
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
                ];
                $this->data[$shadow->resource_type]['disciplines'][$discpline]['resources'][$shadow->resource_id]['budget_cost'] += $shadow->budget_cost;
                $this->data[$shadow->resource_type]['disciplines'][$discpline]['resources'][$shadow->resource_id]['budget_unit'] += $shadow->budget_unit;
                $this->data[$shadow->resource_type]['disciplines'][$discpline]['resources'][$shadow->resource_id]['unit_price'] += $shadow->budget_unit != 0 ? $shadow->budget_cost / $shadow->budget_unit : $shadow->boq_equivilant_rate;
                $this->data[$shadow->resource_type]['disciplines'][$discpline]['resources'][$shadow->resource_id]['curr_unit_price'] += $this->cost_data->get($shadow->resource_id)['curr_unit_price'];
                $this->data[$shadow->resource_type]['disciplines'][$discpline]['resources'][$shadow->resource_id]['to_date_unit_price'] += $this->cost_data->get($shadow->resource_id)['to_date_unit_price'];
                $this->data[$shadow->resource_type]['disciplines'][$discpline]['resources'][$shadow->resource_id]['unit_price_var'] += $this->cost_data->get($shadow->resource_id)['unit_price_var'];
                $this->data[$shadow->resource_type]['disciplines'][$discpline]['resources'][$shadow->resource_id]['cost_variance_to_date_due_unit_price'] += $this->cost_data->get($shadow->resource_id)['cost_variance_to_date_due_unit_price'];
                $this->data[$shadow->resource_type]['disciplines'][$discpline]['resources'][$shadow->resource_id]['cost_variance_completion_due_unit_price'] += $this->cost_data->get($shadow->resource_id)['cost_variance_completion_due_unit_price'];
                $this->data[$shadow->resource_type]['disciplines'][$discpline]['resources'][$shadow->resource_id]['to_date_qty'] += $this->cost_data->get($shadow->resource_id)['to_date_qty'];
                $this->data[$shadow->resource_type]['disciplines'][$discpline]['resources'][$shadow->resource_id]['allowable_qty'] += $this->cost_data->get($shadow->resource_id)['allowable_qty'];
                $this->data[$shadow->resource_type]['disciplines'][$discpline]['resources'][$shadow->resource_id]['qty_var'] += $this->cost_data->get($shadow->resource_id)['qty_var'];
                $this->data[$shadow->resource_type]['disciplines'][$discpline]['resources'][$shadow->resource_id]['cost_variance_to_date_due_qty'] += $this->cost_data->get($shadow->resource_id)['cost_variance_to_date_due_qty'];
                $this->data[$shadow->resource_type]['disciplines'][$discpline]['resources'][$shadow->resource_id]['cost_variance_completion_due_qty'] += $this->cost_data->get($shadow->resource_id)['cost_variance_completion_due_qty'];
            }

        }
        $tree=$this->data;

        return view('reports.cost-control.variance_analysis.variance_analysis', compact('tree', 'project'));
    }

    function buildTypeTree($type)
    {
        $tree = ['id' => $type['id'], 'name' => $type['name'], 'children' => [], 'discpline' => [], 'budget_unit' => 0,
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

                    $tree['discpline'][$cost_account_resource]['unit_price'] = $tree['discpline'][$cost_account_resource]['resources'][$resource->id]['unit_price'];
                    $tree['discpline'][$cost_account_resource]['curr_unit_price'] = $tree['discpline'][$cost_account_resource]['resources'][$resource->id]['curr_unit_price'];
                    $tree['discpline'][$cost_account_resource]['to_date_unit_price'] = $tree['discpline'][$cost_account_resource]['resources'][$resource->id]['to_date_unit_price'];
                    $tree['discpline'][$cost_account_resource]['unit_price_var'] += $tree['discpline'][$cost_account_resource]['resources'][$resource->id]['unit_price_var'];
                    $tree['discpline'][$cost_account_resource]['cost_variance_to_date_due_unit_price'] = $tree['discpline'][$cost_account_resource]['resources'][$resource->id]['cost_variance_to_date_due_unit_price'];
                    $tree['discpline'][$cost_account_resource]['cost_variance_completion_due_unit_price'] += $tree['discpline'][$cost_account_resource]['resources'][$resource->id]['cost_variance_completion_due_unit_price'];
                    $tree['discpline'][$cost_account_resource]['budget_cost'] += $tree['discpline'][$cost_account_resource]['resources'][$resource->id]['budget_cost'];
                    $tree['discpline'][$cost_account_resource]['to_date_qty'] += $tree['discpline'][$cost_account_resource]['resources'][$resource->id]['to_date_qty'];
                    $tree['discpline'][$cost_account_resource]['allowable_qty'] += $tree['discpline'][$cost_account_resource]['resources'][$resource->id]['allowable_qty'];
                    $tree['discpline'][$cost_account_resource]['qty_var'] += $tree['discpline'][$cost_account_resource]['resources'][$resource->id]['qty_var'];
                    $tree['discpline'][$cost_account_resource]['cost_variance_to_date_due_qty'] += $tree['discpline'][$cost_account_resource]['resources'][$resource->id]['cost_variance_to_date_due_qty'];
                    $tree['discpline'][$cost_account_resource]['cost_variance_completion_due_qty'] += $tree['discpline'][$cost_account_resource]['resources'][$resource->id]['cost_variance_completion_due_qty'];

                    $tree['budget_cost'] += $this->resources_data->get($resource->id)['budget_cost'];
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
 unit_price unit_price, sum(budget_cost) budget_cost, sum(curr_unit_price) curr_unit_price, sum(to_date_unit_price) to_date_unit_price, sum(unit_price_var) unit_price_var, sum(cost_variance_to_date_due_unit_price) cost_variance_to_date_due_unit_price, sum(cost_variance_completion_due_unit_price) cost_variance_completion_due_unit_price, sum(to_date_qty) to_date_qty, sum(allowable_qty) allowable_qty, sum((allowable_qty-to_date_qty)) AS qty_var , sum(cost_variance_to_date_due_qty) cost_variance_to_date_due_qty, sum(cost_variance_completion_due_qty) cost_variance_completion_due_qty
FROM break_down_resource_shadows sh JOIN cost_shadows c ON sh.breakdown_resource_id = c.breakdown_resource_id
WHERE sh.project_id = ? AND c.period_id=?
GROUP BY resource_id', [$this->project->id, $this->period_id]));
        return $query;
    }

}