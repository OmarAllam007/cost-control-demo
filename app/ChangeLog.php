<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ChangeLog extends Model
{
    protected $fillable = ['url', 'user_id', 'files', 'method'];

    protected $casts = ['files' => 'array'];

    function changes()
    {
        return $this->hasMany(Change::class);
    }

    function user()
    {
        return $this->belongsTo(User::class);
    }

    function scopeForProjectOnDate(Builder $query, Project $project, Carbon $date)
    {
        $start = $date->startOfDay()->format('Y-m-d H:i:s');
        $end = $date->endOfDay()->format('Y-m-d H:i:s');;

        $query->orderBy('id')->whereRaw("id in (
              select change_log_id from changes where model = 'App\\\\BreakDownResourceShadow' and model_id in (
                select id from break_down_resource_shadows where project_id = {$project->id}
              ) and created_at >= '$start' and created_at <= '$end')");

        return $query->with('changes');
    }

    function getBaseModelNameAttribute()
    {
        return $this->changes()->first()->simple_model_name;
    }
}
