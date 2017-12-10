<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CommunicationUser extends Model
{
    function schedule()
    {
        return $this->belongsTo(CommunicationSchedule::class, 'schedule_id');
    }

    function reports()
    {
        return $this->hasMany(CommunicationReport::class, 'schedule_user_id');
    }
}
