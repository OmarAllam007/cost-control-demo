<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UnitAlias extends Model
{
    protected $fillable = ['unit_id', 'name'];

    public static function createAliasFor($unit_id, $name)
    {
        if (!self::where('name', $name)->exists()) {
            self::create(['unit_id' => $unit_id, 'name' => $name]);
        }
    }
}
