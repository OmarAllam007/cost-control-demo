<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CommunicationSchedule extends Model
{
    protected $fillable = ['project_id', 'type', 'period_id', 'revision_id'];

    function users()
    {
        return $this->hasMany(CommunicationUser::class, 'schedule_id');
    }

    function reports()
    {
        return $this->belongsTo(CommunicationReport::class, 'schedule_id');
    }

    function project()
    {
        return $this->belongsTo(Project::class);
    }

    function period()
    {
        return $this->belongsTo(Period::class);
    }
}
