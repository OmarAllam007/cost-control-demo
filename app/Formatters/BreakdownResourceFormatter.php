<?php
/**
 * Created by PhpStorm.
 * User: hazem
 * Date: 11/7/16
 * Time: 12:07 PM
 */

namespace App\Formatters;


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
        return [
            'id' => $this->resource->id,
            'breakdown_id' => $this->resource->breakdown->id,
            'template' => $this->resource->breakdown->template->name,
            'activity' => $this->resource->breakdown->std_activity->name,
            'cost_account' => $this->resource->breakdown->cost_account,
            'eng_qty' => $this->resource->eng_qty,
            'budget_qty' => $this->resource->budget_qty,
            'resource_qty' => $this->resource->resource_qty,
            'resource_waste' => $this->resource->resource_waste,
            'resource_type' => $this->resource->resource->types->root->name,
            'resource_code' => $this->resource->resource->resource_code,
            'resource_name' => $this->resource->resource->name,
            'unit_price' => $this->resource->resource->rate,
            'measure_unit' => $this->resource->resource->units->type,
            'budget_unit' => $this->resource->budget_unit,
            'budget_cost' => $this->resource->budget_cost,
            'boq_equivilant_rate' => number_format($this->resource->boq_unit_rate, 2),
            'labors_count' => !empty($this->resource->labor_count) ? $this->resource->labor_count : '',
            'productivity_output' => isset($this->resource->project_productivity->after_reduction) ? $this->resource->project_productivity->after_reduction : '',
            'productivity_ref' => isset($this->resource->project_productivity->csi_code) ? $this->resource->project_productivity->csi_code : '',
            'remarks' => $this->resource->remarks,
        ];
    }

    function jsonSerialize()
    {
        return $this->toArray();
    }
}