<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BreakdownVariable extends Model
{
    protected $fillable = ['name', 'value', 'display_order', 'qty_survey_id', 'breakdown_resource_id'];
}
