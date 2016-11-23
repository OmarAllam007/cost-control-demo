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
            'breakdown_resource_id' => $this->resource->id,
            'project_id' =>$this->resource->breakdown->project->id,
            'wbs_id' =>$this->resource->breakdown->wbs_level->id,
            'breakdown_id' => $this->resource->breakdown->id,
            'resource_id' => $this->resource->resource->id,
            'template' => $this->resource->breakdown->template->name,
            'activity' => $this->resource->breakdown->std_activity->name,
            'activity_id' => $this->resource->breakdown->std_activity->id,
            'cost_account' => $this->resource->breakdown->cost_account,
            'eng_qty' => number_format($this->resource->eng_qty, 2),
            'budget_qty' => number_format($this->resource->budget_qty, 2),
            'resource_qty' => number_format($this->resource->resource_qty, 2),
            'resource_waste' => $this->resource->resource_waste,
            'resource_type' => $this->resource->resource->types->root->name,
            'resource_type_id' => $this->resource->resource->types->root->id,
            'resource_code' => $this->resource->resource->resource_code,
            'resource_name' => $this->resource->resource->name,
            'unit_price' => number_format($this->resource->resource->rate, 2),
            'measure_unit' => $this->resource->resource->units->type,
            'budget_unit' => number_format($this->resource->budget_unit, 2),
            'budget_cost' => number_format($this->resource->budget_cost, 2),
            'boq_equivilant_rate' => number_format($this->resource->boq_unit_rate, 2),
            'labors_count' => !empty($this->resource->labor_count) ? $this->resource->labor_count : '',
            'productivity_output' => isset($this->resource->project_productivity->after_reduction) ? $this->resource->project_productivity->after_reduction : '',
            'productivity_ref' => isset($this->resource->project_productivity->csi_code) ? $this->resource->project_productivity->csi_code : '',
            'remarks' => $this->resource->resource->remarks,
        ];
    }

    function jsonSerialize()
    {
        return $this->toArray();
    }
}