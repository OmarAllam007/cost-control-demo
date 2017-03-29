<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MasterShadow extends Model
{
    protected $casts = [
        'wbs' => 'array',
        'activity_divs' => 'array',
        'resource_divs' => 'array',
    ];
}
