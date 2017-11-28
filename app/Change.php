<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Change extends Model
{
    protected $fillable = ['model', 'original', 'updated', 'model_id'];

    protected $casts = ['updated' => 'array', 'original' => 'array'];

    function scopeForProject(Builder $query, Project $project)
    {
        $query->where(function($q) use ($project) {
                $q->where(function ($q) use ($project) {
                    $q->where('model', 'App\Project')->where('model_id', $project->id);
                })->orWhere(function($q) use ($project) {
                    $wbs_levels = $project->wbs_levels()->pluck('id');
                    $q->where('model', 'App\WbsLevel')->whereRaw("model_id in (select id from wbs_levels where project_id = {$project->id})");
                })->orWhere(function($q) use ($project) {
                    $q->where('model', 'App\Resources')->whereRaw("model_id in (select id from resources where project_id = {$project->id})");
                })->orWhere(function($q) use ($project) {
                    $q->where('model', 'App\Breakdown')->whereRaw("model_id in (select id from breakdowns where project_id = {$project->id})");
                })->orWhere(function($q) use ($project) {
                    $q->where('model', 'App\Boq')->whereRaw("model_id in (select id from boqs where project_id = {$project->id})");
                })->orWhere(function($q) use ($project) {
                    $q->where('model', 'App\Survey')->whereRaw("model_id in (select id from qty_surveys where project_id = {$project->id})");
                })->orWhere(function($q) use ($project) {
                    $q->where('model', 'App\Productivity')->whereRaw("model_id in (select id from productivities where project_id = {$project->id})");
                })->orWhere(function($q) use ($project) {
                    $q->where('model', 'App\BreadownResource')->whereRaw("model_id in (select id from breakdown_resources where project_id = {$project->id})");
                });
            })->with('changelog.user');

        dd($query->getQuery()->toSql());
    }

    function changelog()
    {
        return $this->belongsTo(ChangeLog::class);
    }
}
