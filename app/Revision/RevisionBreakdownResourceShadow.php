<?php

namespace App\Revision;

use App\StdActivity;
use App\WbsLevel;
use Illuminate\Database\Eloquent\Model;

class RevisionBreakdownResourceShadow extends Model
{
    function std_activity()
    {
        return $this->belongsTo(StdActivity::class, 'activity_id');
    }

    function wbs()
    {
        return $this->belongsTo(WbsLevel::class, 'wbs_id');
    }
}
