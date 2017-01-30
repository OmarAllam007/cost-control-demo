<?php
/**
 * Created by PhpStorm.
 * User: omar.garana
 * Date: 10/9/2016
 * Time: 2:10 PM
 */

namespace App\Http\Controllers\Reports;

use App\BreakDownResourceShadow;
use App\Project;

class BudgetCostByBreakDownItem
{
    public function compareBudgetCostByBreakDownItem(Project $project)
    {
        set_time_limit(300);
        $totalWeight = 0 ;
        $total = BreakDownResourceShadow::whereProjectId($project->id)->sum('budget_cost');
        $shadows = \DB::table('break_down_resource_shadows as sh')
            ->join('projects', 'sh.project_id', '=', 'projects.id')
            ->join('resource_types', 'sh.resource_type_id', '=', 'resource_types.id')
            ->where('sh.project_id', '=', $project->id)
            ->groupBy('resource_types.name')
            ->selectRaw('SUM(sh.budget_cost) as budget_cost,resource_types.name,resource_types.code')->get();


        $types = [];

        foreach ($shadows as $key => $value) {
            if (!isset($types[$value->name])) {
                $types[$value->name] = [
                    'budget_cost' => 0,
                    'weight' => 0,
                ];
            }
            $types[$value->name]['budget_cost'] += $value->budget_cost;
            $types[$value->name]['weight'] += floatval(($value->budget_cost / $total) * 100);
            $totalWeight += $types[$value->name]['weight'];
        }
        $this->compareBudgetCostByBreakDownItemChart($types);
        return view('reports.budget_cost_by_break_down', compact('types', 'totalWeight', 'project','total'));
    }

    public function compareBudgetCostByBreakDownItemChart($data)
    {
        $item = \Lava::DataTable();
        $item->addStringColumn('Resource Type')->addNumberColumn('Weight');
        foreach ($data as $key => $value) {

            $item->addRow([$key, $data[$key]['weight']]);
        }
        \Lava::PieChart('BreakDown', $item, [
            'width' => '1000',
            'height' => '600',
            'title' => 'Budget Cost | % Weight',
            'is3D' => true,
            'slices' => [
                ['offset' => 0.0],
                ['offset' => 0.0],
                ['offset' => 0.0],
            ],
            'pieSliceText' => "label",
            'sliceVisibilityThreshold' => 0,
        ]);

    }
}