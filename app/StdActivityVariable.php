<?php

namespace App;

use App\Behaviors\HasChangeLog;
use App\Behaviors\RecordsUser;
use Illuminate\Database\Eloquent\Model;

class StdActivityVariable extends Model
{
    use HasChangeLog, RecordsUser;

    protected $fillable = ['label', 'display_order', 'std_activity_id'];

    function std_activity()
    {
        return $this->belongsTo(StdActivity::class, 'std_activity_id');
    }
}
