<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ActualBatch extends Model
{
    protected $fillable = ['user_id', 'type', 'file', 'project_id', 'period_id'];

    function project()
    {
        return $this->belongsTo(Project::class);
    }

    function issues()
    {
        $relation = $this->hasMany(CostIssue::class, 'batch_id');
        $relation->orderBy('id');
        return $relation;
    }

    function user()
    {
        return $this->belongsTo(User::class);
    }
}
