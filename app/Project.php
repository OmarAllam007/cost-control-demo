<?php

namespace App;

use App\Behaviors\HasOptions;
use App\Behaviors\Tree;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use SoftDeletes, HasOptions, Tree;

    protected static $alias = 'Project';

    protected $fillable = [
        'name',
        'project_code',
        'client_name',
        'project_location'
        ,
        'project_contract_value',
        'project_start_date',
        'project_duration',
        'description'
    ];

    protected $dates = ['created_at', 'updated_at'];

    function wbs_levels()
    {
        return $this->hasMany(WbsLevel::class);
    }

    function getWbsTreeAttribute()
    {
        return $this->wbs_levels()->tree()->get();
    }

    function breakdowns()
    {
        return $this->hasMany(Breakdown::class);
    }

    function breakdown_resources()
    {
        return $this->hasManyThrough(BreakdownResource::class, Breakdown::class);
    }

    function quantities()
    {
        return $this->hasMany(Survey::class);
    }

    function getPlainResourcesAttribute()
    {
        return $this->breakdown_resources->load('resource.resource')
            ->pluck('resource.resource')->unique();
    }

}