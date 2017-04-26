<?php
/**
 * Created by PhpStorm.
 * User: hazem
 * Date: 11/7/16
 * Time: 12:07 PM
 */

namespace App\Formatters;


use App\Boq;
use App\BreakdownResource;

class BreakdownResourceFormatter implements \JsonSerializable
{
    /**
     * @var BreakdownResource
     */
    private $resource;

    function __construct(BreakdownResource $resource)
    {
        $this->resource = $resource;
    }

    function toArray()
    {
//        $budget_qty = $this->resource->breakdown->wbs_level->getBudgetQty($this->resource->breakdown->cost_account);
//        $eng_qty = $this->resource->breakdown->wbs_level->getEngQty($this->resource->breakdown->cost_account);
        $boq = Boq::costAccountOnWbs($this->resource->breakdown->wbs_level, $this->resource->breakdown->cost_account)->first();
        return [
            'breakdown_resource_id' => $this->resource->id,
            'code' => $this->resource->code,
            'project_id' => $this->resource->breakdown->project->id,
            'wbs_id' => $this->resource->breakdown->wbs_level->id ?? '',
            'breakdown_id' => $this->resource->breakdown->id,
            'resource_id' => $this->resource->resource_id ?? '',
            'template' => $this->resource->breakdown->template->name ?? '',
            'activity' => $this->resource->breakdown->std_activity->name,
            'activity_id' => $this->resource->breakdown->std_activity->id,
            'cost_account' => $this->resource->breakdown->cost_account,
            'resource_waste' => $this->resource->resource_waste,
            'eng_qty' => $this->resource->eng_qty,
            'budget_qty' => $this->resource->budget_qty,
            'resource_qty' => $this->resource->resource_qty,
            'resource_type' => $this->resource->resource->types->root->name ?? 'Not Assigned',
            'resource_type_id' => $this->resource->resource->types->root->id ?? 0,
            'resource_code' => $this->resource->resource->resource_code ?? '',
            'resource_name' => $this->resource->resource->name ?? 'Not Assigned',
            'unit_price' => $this->resource->resource->rate ?? 0,
            'measure_unit' => $this->resource->resource->units->type ?? 'Not Assigned',
            'budget_unit' =>$this->resource->budget_unit,
            'budget_cost' => $this->resource->budget_cost,
            'boq_equivilant_rate' => $this->resource->boq_unit_rate,
            'labors_count' => !empty($this->resource->labor_count) ? $this->resource->labor_count : '',
            'productivity_output' => isset($this->resource->project_productivity->after_reduction) ? $this->resource->project_productivity->after_reduction : '',
            'productivity_ref' => isset($this->resource->project_productivity->csi_code) ? $this->resource->project_productivity->csi_code : '',
            'remarks' => $this->resource->remarks,
            'productivity_id'=>$this->resource->project_productivity->id ?? 0,
            'template_id'=>$this->resource->breakdown->template->id,
            'unit_id'=>$this->resource->resource->units->id??0,
            'boq_id' => $boq->id ?? 0,
            'boq_wbs_id' => $boq->wbs_id ?? 0
        ];
    }

    function jsonSerialize()
    {
        return $this->toArray();
    }
}