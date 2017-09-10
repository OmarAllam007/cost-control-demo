<?php
/**
 * Created by PhpStorm.
 * User: hazem
 * Date: 8/14/17
 * Time: 8:34 AM
 */

namespace App\Reports\Budget;


use App\BreakDownResourceShadow;
use App\Project;
use App\ResourceType;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Classes\LaravelExcelWorksheet;
use Maatwebsite\Excel\Writers\LaravelExcelWriter;

class ManPowerReport
{
    /** @var Project */
    protected $project;

    /** @var Collection */
    protected $resources;

    /** @var int */
    protected $row = 1;

    /** @var Collection */
    protected $tree;

    /** @var Collection */
    protected $resourceTypes;

    public function __construct(Project $project)
    {
        $this->project = $project;
    }

    public function run()
    {
        $this->resources = BreakDownResourceShadow::from('break_down_resource_shadows as sh')
            ->where('sh.project_id', $this->project->id)
            ->where('sh.resource_type_id', 2)
            ->selectRaw(
                'r.resource_type_id, sh.resource_id, sh.resource_code, sh.resource_name, ' .
                'sh.measure_unit, sum(sh.budget_unit) budget_unit, sum(sh.budget_cost) budget_cost'
            )->join('resources as r', 'sh.resource_id', '=', 'r.id')
            ->groupBy(['r.resource_type_id', 'sh.resource_id', 'sh.resource_code', 'sh.resource_name', 'sh.measure_unit'])
            ->orderBy('resource_name')
            ->get()->groupBy('resource_type_id');

        $this->resourceTypes = ResourceType::whereParentId(2)->get()->groupBy('parent_id');

        $this->tree = $this->buildTree();

        return ['project' => $this->project, 'tree' => $this->tree];
    }

    public function excel()
    {
        $this->run();

        \Excel::create(slug($this->project->name) . '_man-power', function (LaravelExcelWriter $writer) {
            $writer->sheet('Man Power', function (LaravelExcelWorksheet $sheet) {
                $sheet->row(1, ['Description', 'Code', 'Budget Cost', 'Budget Unit', 'Unit of Measure']);
                $sheet->cells('A1:E1', function ($cells) {
                    $cells->setFont(['bold' => true])->setBackground('#3f6caf')->setFontColor('#ffffff');
                });

                $this->resources->each(function ($resource) use ($sheet) {
                    $sheet->row(++$this->row, [
                        $resource->resource_name, $resource->resource_code, $resource->budget_cost,
                        $resource->budget_unit, $resource->measure_unit
                    ]);
                });

                $sheet->getColumnDimension('A')->setWidth(100);
                $sheet->setAutoFilter();
                $sheet->setAutoSize(['B', 'C', 'D', 'E']);
                $sheet->setAutoSize(false);
            });

            $writer->download('xlsx');
        });

    }

    protected function buildTree($parent = 2)
    {
        $tree = $this->resourceTypes->get($parent) ?? collect();

        $tree = $tree->filter(function ($subtype) {
            return $this->resources->has($subtype->id);
        })->map(function (ResourceType $type) {
            $type->subtypes = $this->buildTree($type->id);
            $type->labours = $this->resources->get($type->id);

            $type->budget_cost = $type->labours->sum('budget_cost') + $type->subtypes->sum('budget_cost');


            return $type;
        })->reject(function (ResourceType $type) {
            return $type->subtypes->isEmpty() && $type->labours->isEmpty();
        });

        return $tree;
    }
}