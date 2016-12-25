<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ActivityMap extends Model
{
    protected $fillable = ['project_id', 'activity_code', 'equiv_code'];

    function scopeForProject(Builder $query, $project)
    {
        return $query->where('project_id', $project->id);
    }
}
