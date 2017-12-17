<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CommunicationUser extends Model
{
    protected $fillable = ['user_id'];

    protected $dates = ['created_at', 'updated_at', 'sent_at'];

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
        $query->whereNull('sent_at');
    }
}
