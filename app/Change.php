<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Change extends Model
{
    protected $fillable = ['model', 'original', 'updated', 'model_id'];

    protected $casts = ['updated' => 'array', 'original' => 'array'];
}
