<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ActualBatch extends Model
{
    protected $fillable = ['user_id', 'type'];
}
