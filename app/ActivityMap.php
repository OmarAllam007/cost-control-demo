<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ActivityMap extends Model
{
    protected $fillable = ['project_id', 'activity_code', 'equiv_code'];
}
