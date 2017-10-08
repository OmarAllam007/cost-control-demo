<?php

namespace App\Reports\Budget;

use App\Boq;
use App\BreakDownResourceShadow;
use App\Project;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Classes\LaravelExcelWorksheet;

class CharterReport
{
    /** @var Project */
    protected $project;

    /** @var Collection */
    protected $resource_types;

    /** @var Collection */
    protected $disciplines;

    /** @var float */
    protected $total;

    function __construct(Project $project)
    {
        $this->project = $project;
    }

    function run()
    {
        $this->total = BreakDownResourceShadow::where('project_id', $this->project->id)->sum('budget_cost');

        $this->resource_types = BreakDownResourceShadow::where('project_id', $this->project->id)
            ->selectRaw('resource_type as type, sum(budget_cost) as budget_cost')
            ->groupBy('resource_type')->orderBy('resource_type')
            ->get()->map(function($type) {
                $type->weight = $type->budget_cost * 100 / $this->total;
                return $type;
            });

        $this->disciplines = BreakDownResourceShadow::where('project_id', $this->project->id)
            ->selectRaw('a.discipline as discipline, sum(budget_cost) as budget_cost')
            ->join('std_activities as a', 'activity_id', '=', 'a.id')
            ->groupBy('a.discipline')->orderBy('a.discipline')
            ->get()->map(function ($discipline) {
                $discipline->weight = $discipline->budget_cost * 100 / $this->total;
                return $discipline;
            });

        $this->project->general_requirements = BreakDownResourceShadow::where('project_id', $this->project->id)
            ->where('resource_type_id', 1)
            ->selectRaw('sum(budget_cost) as cost')->value('cost');

        $this->project->management_reserve = BreakDownResourceShadow::where('project_id', $this->project->id)
            ->where('resource_type_id', 8)
            ->selectRaw('sum(budget_cost) as cost')->value('cost');

        $this->project->direct_cost = $this->total - $this->project->general_requirements - $this->project->management_reserve;

        $this->project->profit = $this->project->project_contract_signed_value -
            $this->project->change_order_amount - $this->total;

        return [
            'project' => $this->project, 'resource_types' => $this->resource_types,
            'disciplines' => $this->disciplines, 'total' => $this->total
        ];
    }

    function excel()
    {

    }

    function sheet(LaravelExcelWorksheet $sheet)
    {

    }
}