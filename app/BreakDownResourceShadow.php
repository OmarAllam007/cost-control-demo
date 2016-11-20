<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class BreakDownResourceShadow extends Model
{
    protected $fillable = ['breakdown_id', 'std_activity_resource_id', 'wbs_level', 'budget_qty', 'eng_qty', 'resource_waste', 'labor_count', 'remarks', 'productivity_id', 'remarks', 'code', 'resource_qty', 'resource_id', 'equation'];


}
