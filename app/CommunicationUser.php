<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CommunicationUser extends Model
{
    protected $fillable = ['user_id'];

    function schedule()
    {
        return $this->belongsTo(CommunicationSchedule::class, 'schedule_id');
    }

    function reports()
    {
        return $this->hasMany(CommunicationReport::class, 'schedule_user_id');
    }

    function user()
    {
        return $this->belongsTo(ProjectRole::class, 'user_id');
    }

    function scopeNotSent($query)
    {
        return $query->whereNull('sent_at');
    }
}
