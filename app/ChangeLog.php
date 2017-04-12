<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChangeLog extends Model
{
    protected $fillable = ['url', 'user_id', 'files', 'method'];

    protected $casts = ['files' => 'array'];

    function changes()
    {
        return $this->hasMany(Change::class);
    }
}
