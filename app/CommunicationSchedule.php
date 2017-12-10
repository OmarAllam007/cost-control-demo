<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CommunicationSchedule extends Model
{
    function users()
    {
        return $this->belongsTo(CommunicationUser::class, 'schedule_id');
    }
}
