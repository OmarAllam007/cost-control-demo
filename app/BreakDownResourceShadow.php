<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class BreakDownResourceShadow extends Model
{
    protected $fillable = [
        'breakdown_resource_id',
        'project_id',
        'wbs_id',
        'breakdown_id',
        'activity_id',
        'resource_type_id',
        'template',
        'activity',
        'cost_account',
        'eng_qty',
        'budget_qty',
        'resource_qty',
        'resource_waste',
        'resource_type',
        'resource_code',
        'resource_name',
        'unit_price',
        'measure_unit',
        'budget_unit',
        'budget_cost',
        'boq_equivilant_rate',
        'labors_count',
        'productivity_output',
        'productivity_ref',
        'remarks',
    ];


}
