<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    protected $fillable = ['wbs_level_id', 'project_id', 'std_activity_id'];

}
