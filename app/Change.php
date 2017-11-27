<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Change extends Model
{
    protected $fillable = ['model', 'original', 'updated', 'model_id'];

    protected $casts = ['updated' => 'array', 'original' => 'array'];

    function scopeForProjectd(Builder $query, Project $project)
    {
        $query->where('project_id', $project->id)
            ->orWhere(function($q) {

            });
    }
}
