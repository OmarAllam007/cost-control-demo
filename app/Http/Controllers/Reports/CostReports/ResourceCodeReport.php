<?php
/**
 * Created by PhpStorm.
 * User: omar
 * Date: 26/12/16
 * Time: 11:51 ص
 */

namespace App\Http\Controllers\Reports\CostReports;


use App\BreakDownResourceShadow;
use App\BusinessPartner;
use App\CostShadow;
use App\MasterShadow;
use App\Period;
use App\Project;
use App\Resources;
use App\ResourceType;
use Illuminate\Database\Eloquent\Builder;

class ResourceCodeReport
{
    /** @var Project */
    protected $project;
    /** @var Period */
    protected $period;

    function __construct(Project $project, $chosen_period_id)
    {
        $this->project = $project;
        $this->period = Period::find($chosen_period_id);
    }

    public function run()
    {
        $tree = $this->buildTree();
        $project = $this->project;

        $periods = $project->periods()->readyForReporting()->pluck('name', 'id');

        $types = BreakDownResourceShadow::whereProjectId($this->project->id)
            ->selectRaw('DISTINCT resource_type')->orderBy('resource_type')->pluck('resource_type');

        $disciplines = MasterShadow::wherePeriodId($this->period->id)
            ->selectRaw('DISTINCT boq_discipline')->orderBy('boq_discipline')->pluck('boq_discipline');

        $topMaterials = MasterShadow::wherePeriodId($this->period->id)
            ->selectRaw('DISTINCT top_material')->orderBy('top_material')->pluck('top_material')->filter();

        return view('reports.cost-control.resource_code.resource_code',
            compact('project', 'tree', 'periods', 'types', 'disciplines', 'topMaterials'));
    }

    private function buildTree()
    {
        $query = MasterShadow::forPeriod($this->period)->resourceDictReport();

        $resourceData = $this->applyFilters($query)->get();

        $tree = $resourceData->groupBy('resource_type')->map(function($typeGroup) {
            return $typeGroup->groupBy('boq_discipline')->map(function($disciplineGroup) {
                return $disciplineGroup->groupBy('top_material');
            });
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

        if ($resource = $request->get('resource')) {
            $query->where(function($q) use ($resource) {
                $term = "%$resource%";
                $q->where('resource_code', 'like', $term)->orWhere('resource_name', 'like', $term);
            });
        }

        if ($request->exists('negative_to_date')) {
            $query->havingRaw('to_date_allowable - to_date_cost < 0');
        }

        if ($request->exists('negative_completion')) {
            $query->having('cost_var', '<', 0);
        }

        return $query;
    }

}