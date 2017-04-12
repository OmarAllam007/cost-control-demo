<?php

namespace App;

use App\Behaviors\HasChangeLog;
use Illuminate\Database\Eloquent\Model;

class ActualBatch extends Model
{
    use HasChangeLog;

    protected $fillable = ['user_id', 'type', 'file', 'project_id', 'period_id'];

    protected $appends = ['uploaded_by', 'uploaded_at', 'period_name'];

    function project()
    {
        return $this->belongsTo(Project::class);
    }

    function period()
    {
        return $this->belongsTo(Period::class);
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

    function getUploadedByAttribute()
    {
        return $this->user->name;
    }

    function getUploadedAtAttribute()
    {
        return $this->created_at->format('d/m/Y h:i A');
    }

    function getPeriodNameAttribute()
    {
        return $this->period->name;
    }
}
