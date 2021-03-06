<?php
/**
 * Created by PhpStorm.
 * User: hazem
 * Date: 18/2/18
 * Time: 5:16 PM
 */

namespace App\Formatters;


use Illuminate\Contracts\Support\Jsonable;

class RollupResourceFormatter implements Jsonable
{

    private $resource;

    public function __construct($resource)
    {
        $this->resource = $resource;
    }

    function toArray()
    {
        return [
            'id' => $this->resource->breakdown_resource_id,
            'code' => $this->resource->resource_code,
            'name' => $this->resource->resource_name,
            'remarks' => $this->resource->remarks,
            'budget_unit' => $this->resource->budget_unit,
            'budget_qty' => $this->resource->budget_qty,
            'measure_unit' => $this->resource->measure_unit,
            'qs_unit' => $this->resource->survey->unit_id ?? 0,
            'budget_cost' => $this->resource->budget_cost,
            'to_date_cost' => $this->resource->to_date_cost,
            'to_date_qty' => $this->resource->to_date_qty,
            'important' => $this->resource->important,
            'progress' => $this->resource->progress,
            'selected' => false,
        ];
    }

    function toJson($options = 0)
    {
        return json_encode($this->toArray());
    }
}