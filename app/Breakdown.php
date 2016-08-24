<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Breakdown extends Model
{
    protected $fillable = ['std_activity_id', 'template_id', 'name', 'cost_account', 'project_id', 'wbs_level_id'];

    function resources()
    {
        return $this->hasMany(BreakdownResource::class, 'breakdown_id');
    }
}
