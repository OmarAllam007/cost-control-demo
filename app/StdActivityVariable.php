<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StdActivityVariable extends Model
{
    protected $fillable = ['label', 'display_order', 'std_activity_id'];

    function std_activity()
    {
        return $this->belongsTo(StdActivity::class, 'std_activity_id');
    }
}
