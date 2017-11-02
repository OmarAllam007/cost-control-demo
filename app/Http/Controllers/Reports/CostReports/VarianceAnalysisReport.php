<?php
namespace App\Http\Controllers\Reports\CostReports;

use App\Boq;
use App\BreakDownResourceShadow;
use App\CostShadow;
use App\MasterShadow;
use App\Period;
use App\Project;
use App\ResourceType;
use App\StdActivity;
use App\Survey;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class VarianceAnalysisReport
{

    /** @var Period */
    private $period;

    /** @var Project */
    private $project;

    public function __construct(Period $period)
    {
        $this->period = $period;
        $this->project = $period->project;
    }

    function run()
    {
        $project = $this->project;
        $period = $this->period;
        $tree = $this->buildTree();

        $periods = $project->periods()->readyForReporting()->pluck('name', 'id');

        $types = BreakDownResourceShadow::whereProjectId($this->project->id)
            ->selectRaw('DISTINCT resource_type')->orderBy('resource_type')->pluck('resource_type');

        $disciplines = MasterShadow::wherePeriodId($this->period->id)
            ->selectRaw('DISTINCT boq_discipline')->orderBy('boq_discipline')->pluck('boq_discipline');

        $topMaterials = MasterShadow::wherePeriodId($this->period->id)
            ->selectRaw('DISTINCT top_material')->orderBy('top_material')->pluck('top_material')->filter();

        return compact('tree', 'project', 'periods', 'types', 'disciplines', 'period', 'topMaterials');
    }

    function buildTree()
    {
        $query = MasterShadow::forPeriod($this->period)->varAnalysisReport();

        $resourceData = $this->applyFilters($query)->get();

        $tree = $resourceData->groupBy('resource_type')->map(function($typeGroup) {
            $disciplines = $typeGroup->groupBy('boq_discipline')->map(function($group) {
                $resources = $group->map(function($resource) {
                    $resource->price_var = $resource->budget_unit_price - $resource->to_date_unit_price;
                    $resource->price_cost_var = $resource->price_var * $resource->to_date_qty;

                    $resource->qty_var = $resource->to_date_allowable_qty - $resource->to_date_qty;
                    $resource->qty_cost_var = $resource->qty_var * $resource->budget_unit_price;

                    return $resource;
                });

                return [
                    'resources' => $resources, 'price_cost_var' => $resources->sum('price_cost_var'),
                    'qty_cost_var' => $resources->sum('qty_cost_var'),
                    'cost_unit_price_var' => $resources->sum('cost_unit_price_var'),
                    'cost_qty_var' => $resources->sum('cost_qty_var')
                ];
                //, 'to_date_variance' => $group->sum()];
            });

            return [
                'disciplines' => $disciplines, 'price_cost_var' => $disciplines->sum('price_cost_var'),
                'qty_cost_var' => $disciplines->sum('qty_cost_var'),
                'cost_unit_price_var' => $disciplines->sum('cost_unit_price_var'),
                'cost_qty_var' => $disciplines->sum('cost_qty_var')
            ];
        });

        return $tree;
    }

    protected function applyFilters(Builder $query)
    {
        $request = request();

        if ($status = strtolower($request->get('status', ''))) {
            if ($status == 'not started') {
                $query->havingRaw('sum(to_date_qty) = 0');
            } elseif ($status == 'in progress') {
                $query->havingRaw('sum(to_date_qty) > 0 AND AVG(progress) < 100');
            } elseif ($status == 'closed') {
                $query->where('to_date_qty', '>', 0)->where('progress', 100);
            }
        }

        // We are doing like here because data is not clean and some types are repeated with spaces
        // After data cleaning, where this still valid, we can safely rely on resource_type_id
        if ($type = $request->get('type')) {
            // rt is the alias for joined resource type table
            $query->where('rt.name', 'like', "%$type%");
        }

        if ($discipline = $request->get('discipline')) {
            // We have to consider that resources without discipline are mapped to general also
            if (strtolower($discipline) == 'general') {
                $query->where(function($q) {
                    $q->where('boq_discipline', 'general')->orWhere('boq_discipline', '')->orWhereNull('boq_discipline');
                });
            } else {
                $query->where('boq_discipline', $discipline);
            }
        }

        if ($top = $request->get('top')) {
            // We have to consider that resources without discipline are mapped to general also
            if (strtolower($top) == 'all') {
                $query->whereNotNull('top_material')->where('top_material', '!=', '');
            } elseif (strtolower($top) == 'other') {
                $query->where(function($q) {
                    $q->whereNull('top_material')->orWhere('top_material', '');
                });
            } else {
                $query->where('top_material', $top);
            }
        }

        if ($resource = $request->get('resource')) {
            $query->where(function($q) use ($resource) {
                $term = "%$resource%";
                $q->where('resource_code', 'like', $term)->orWhere('resource_name', 'like', $term);
            });
        }

        return $query;
    }
}