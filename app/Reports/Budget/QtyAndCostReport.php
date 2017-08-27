<?php

namespace App\Reports\Budget;

use App\BreakDownResourceShadow;
use App\Project;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Classes\LaravelExcelWorksheet;
use Maatwebsite\Excel\Writers\LaravelExcelWriter;

class QtyAndCostReport
{
    /** @var Project */
    protected $project;

    /** @var Collection */
    protected $disciplines;

    /** @var int */
    protected $row = 1;

    public function __construct(Project $project)
    {
        $this->project = $project;
    }

    function run()
    {
        $this->disciplines = collect(\DB::select('SELECT type, sum((budget_price - dry_price) * budget_qty) AS cost_diff, sum((budget_qty - dry_qty) * budget_price) AS qty_diff FROM (
  SELECT concat(sh.boq_wbs_id, sh.cost_account), a.discipline AS type,
    sum(sh.boq_equivilant_rate) AS budget_price, avg(boqs.dry_ur) AS dry_price,
   avg(qs.budget_qty) AS budget_qty, avg(boqs.quantity) AS dry_qty
  FROM break_down_resource_shadows sh
    LEFT JOIN boqs ON (sh.boq_id = boqs.id)
    LEFT JOIN std_activities a ON sh.activity_id = a.id
    LEFT JOIN qty_surveys qs ON (sh.survey_id = qs.id)
  WHERE sh.project_id = 35
  GROUP BY 1, 2
) AS data GROUP BY  type'));

        return ['project' => $this->project, 'disciplines' => $this->disciplines];
    }

    function excel()
    {
        \Excel::create(slug($this->project->name), function (LaravelExcelWriter $writer) {
            $writer->sheet('', function (LaravelExcelWorksheet $sheet) {
                $this->sheet($sheet);
            });

            $writer->download('xlsx');
        });
    }

    function sheet(LaravelExcelWorksheet $sheet)
    {
        $this->run();

        return $sheet;
    }
}