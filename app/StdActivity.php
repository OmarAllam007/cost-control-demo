<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StdActivity extends Model
{
    protected $fillable = ['code', 'name', 'division_id', 'id_partial'];

    protected $dates = ['created_at', 'updated_at'];

    public function division()
    {
        return $this->belongsTo(ActivityDivision::class, 'division_id');
    }
}