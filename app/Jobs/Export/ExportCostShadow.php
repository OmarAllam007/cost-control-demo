<?php

namespace App\Jobs\Export;

use App\BreakDownResourceShadow;
use App\CostShadow;
use App\Jobs\Job;
use App\WbsLevel;
use App\WbsResource;

class ExportCostShadow extends Job
{

    protected $project;
    /**
     * @var string
     */
    private $perspective;

    public function __construct($project, $perspective = '')
    {
        $this->project = $project;
        $this->perspective = $perspective;
    }


    public function handle()
    {
        $file = storage_path('app/' . uniqid('cost_shadow_') . '.csv');

        $headers = [
            'WBS',
            'Activity Name',
            'Activity ID',
            'Breakdown Template',
            'Cost Account',
            'Eng Quantity',
            'Budget Quantity',
            'Resource Quantity',
            'Resource Waste',
            'Resource Type',
            'Resource Code',
            'Resource Name',
            'Price/Unit',
            'Unit Of Measure',
            'Budget Unit',
            'Budget Cost',
            'BOQ Equivalent Unit rate',
            'No. Of Labors',
            'Productivity (Unit/Day)',
            'Productivity Ref',
            'Remarks',
            'Progress',
            'Status',
            'Prev. Price/Unit',
            'Prev. Quantity',
            'Prev. Cost',
            'Current. Price/Unit',
            'Current Quantity',
            'Current Cost',
            'To Date Price/Unit(Eqv)',
            'To Date Quantity',
            'To Date Cost',
            'Allowable (EV) cost',
            'Var +/-',
            'Remaining Price/Unit',
            'Remaining Qty',
            'Remaining Cost',
            'BL Allowable Cost',
            'Var +/- 10',
            'Completion Price/Unit',
            'Completion Qty',
            'Completion Cost',
            'Price/Unit Var',
            'Qty Var +/-',
            'Cost Var +/-',
            'Physical Unit',
            '(P/W) Index',
            'Cost Variance To Date Due to Unit Price',
            'Allowable Quantity',
            'Cost Variance Remaining Due to Unit Price',
            'Cost Variance Completion Due to Unit Price',
            'Cost Variance Completion Due to Qty',
            'Cost Variance to Date Due to Qty',
        ];

        $lines = [implode(',', array_map('csv_quote', $headers))];

        $period = $this->project->open_period();

        if ($this->perspective == 'budget') {
            $shadows = BreakDownResourceShadow::with(['wbs', 'wbs.parent.parent.parent'])->joinCost(null, $period)->where('budget.project_id', $this->project->id)->get();
        } else {
            $shadows = CostShadow::with(['wbs', 'wbs.parent.parent.parent'])->joinShadow(null, $period)->where('bsh.project_id', $this->project->id)->get();
        }

        foreach ($shadows as $costShadow) {
           /* $levels = [];

            $parent = $costShadow->wbs;
            $levels[] = $costShadow->wbs->name;
            $parent = $parent->parent;

            while ($parent) {
                $levels[] = $parent->name;
                $parent = $parent->parent;
            };
            $levels = array_reverse($levels);*/

            $lines[] = implode(',', array_map('csv_quote', [
//                $costShadow->wbs->canonical,
                $costShadow['activity'],
                $costShadow['code'],
                $costShadow['template'],
                $costShadow['cost_account'],
                number_format($costShadow['eng_qty'] ?: '0', 2, '.', ''),
                number_format($costShadow['budget_qty'] ?: '0', 2, '.', ''),
                number_format($costShadow['resource_qty'] ?: '0', 2, '.', ''),
                number_format($costShadow['resource_waste'] ?: '0', 2, '.', ''),
                $costShadow['resource_type'],
                $costShadow['resource_code'],
                $costShadow['resource_name'],
                number_format($costShadow['unit_price'] ?: '0', 2, '.', ''),
                $costShadow['measure_unit'],
                number_format($costShadow['budget_unit'] ?: '0', 2, '.', ''),
                number_format($costShadow['budget_cost'] ?: '0', 2, '.', ''),
                number_format($costShadow['boq_equivilant_rate'] ?: '0', 2, '.', ''),
                number_format($costShadow['labors_count'], 2, '.', ''),
                number_format($costShadow['productivity_output'] ?: '0', 2, '.', ''),
                $costShadow['productivity_ref'] ?: '0',
                $costShadow['remarks'],
                number_format($costShadow->progress, 2, '.', ''),
                $costShadow->status,
                number_format($costShadow['prev_unit_price'] ?: '0', 2, '.', ''),
                number_format($costShadow['prev_qty'] ?: '0', 2, '.', ''),
                number_format($costShadow['prev_cost'] ?: '0', 2, '.', ''),
                number_format($costShadow['curr_unit_price'] ?: '0', 2, '.', ''),
                number_format($costShadow['curr_qty'] ?: '0', 2, '.', ''),
                number_format($costShadow['curr_cost'] ?: '0', 2, '.', ''),
                number_format($costShadow['to_date_unit_price'] ?: '0', 2, '.', ''),
                number_format($costShadow['to_date_qty'] ?: '0', 2, '.', ''),
                number_format($costShadow['to_date_cost'] ?: '0', 2, '.', ''),
                number_format($costShadow['allowable_ev_cost'] ?: '0', 2, '.', ''),
                number_format($costShadow['allowable_var'] ?: '0', 2, '.', ''),
                number_format($costShadow['remaining_unit_price'] ?: '0', 2, '.', ''),
                number_format($costShadow['remaining_qty'] ?: '0', 2, '.', ''),
                number_format($costShadow['remaining_cost'] ?: '0', 2, '.', ''),
                number_format($costShadow['bl_allowable_cost'] ?: '0', 2, '.', ''),
                number_format($costShadow['bl_allowable_var'] ?: '0', 2, '.', ''),
                number_format($costShadow['completion_unit_price'] ?: '0', 2, '.', ''),
                number_format($costShadow['completion_qty'] ?: '0', 2, '.', ''),
                number_format($costShadow['completion_cost'] ?: '0', 2, '.', ''),
                number_format($costShadow['unit_price_var'] ?: '0', 2, '.', ''),
                number_format($costShadow['qty_var'] ?: '0', 2, '.', ''),
                number_format($costShadow['cost_var'] ?: '0', 2, '.', ''),
                number_format($costShadow['physical_unit'] ?: '0', 2, '.', ''),
                number_format($costShadow['pw_index'] ?: '0', 2, '.', ''),
                number_format($costShadow['cost_variance_to_date_due_unit_price'] ?: '0', 2, '.', ''),
                number_format($costShadow['allowable_qty'] ?: '0', 2, '.', ''),
                number_format($costShadow['cost_variance_remaining_due_unit_price'] ?: '0', 2, '.', ''),
                number_format($costShadow['cost_variance_completion_due_unit_price'] ?: '0', 2, '.', ''),
                number_format($costShadow['cost_variance_completion_due_qty'] ?: '0', 2, '.', ''),
                number_format($costShadow['cost_variance_to_date_due_qty'] ?: '0', 2, '.', ''),
            ]));
        }

        return implode(PHP_EOL, $lines);
    }

    function styleColumns($sheet, $range, $color)
    {
        $sheet->getStyle("" . $range . "")->applyFromArray(
            array(
                'fill' => array(
                    'type' => \PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => $color)
                )
            ));
    }
}
