<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CommunicationSchedule extends Model
{
    protected $fillable = ['project_id', 'type'];

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
}
