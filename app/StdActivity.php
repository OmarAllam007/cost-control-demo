<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StdActivity extends Model
{
    protected $fillable = ['code', 'name', 'division_id', 'id_partial'];

    protected $dates = ['created_at', 'updated_at'];

    public static function options()
    {
        return self::orderBy('name')->pluck('name', 'id')->prepend('Select Activity', '');
    }

    public function division()
    {
        return $this->belongsTo(ActivityDivision::class, 'division_id');
    }

    public function breakdowns()
    {
        return $this->hasMany(BreakdownTemplate::class);
    }
}