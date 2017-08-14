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

    public function __construct(Project $project)
    {
        $this->project = $project;
    }

    public function run()
    {
        $this->resources = BreakDownResourceShadow::whereProjectId($this->project->id)
            ->whereResourceTypeId(2)
            ->selectRaw('resource_id, resource_code, resource_name, measure_unit, sum(budget_unit) budget_unit, sum(budget_cost) budget_cost')
            ->groupBy(['resource_id', 'resource_code', 'resource_name', 'measure_unit'])
            ->orderBy('resource_name')
            ->get();

        return ['project' => $this->project, 'resources' => $this->resources];
    }

    public function excel()
    {
        $this->run();

        \Excel::create(slug($this->project->name) . '_man-power', function(LaravelExcelWriter $writer) {
            $writer->sheet('Man Power', function (LaravelExcelWorksheet $sheet) {
                $sheet->row(1, ['Description', 'Code', 'Budget Cost', 'Budget Unit', 'Unit of Measure']);
                $sheet->cells('A1:E1', function($cells) {
                    $cells->setFont(['bold' => true])->setBackground('#3f6caf')->setFontColor('#ffffff');
                });

                $this->resources->each(function($resource) use ($sheet) {
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
}