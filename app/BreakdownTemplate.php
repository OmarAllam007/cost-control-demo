<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BreakdownTemplate extends Model
{
    protected $fillable = ['name', 'code', 'std_activity_id'];

    protected $dates = ['created_at', 'updated_at'];

    public function activity()
    {
        return $this->belongsTo(StdActivity::class, 'std_activity_id');
    }
}