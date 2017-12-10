<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CommunicationReport extends Model
{
    function user()
    {
        return $this->belongsTo(CommunicationUser::class, 'schedule_user_id');
    }

    function report()
    {
        return $this->belongsTo(Report::class);
    }
}
