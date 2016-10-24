<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UnitAlias extends Model
{
    protected $fillable = ['unit_id', 'name'];

    public static function createAliasFor($unit_id, $name)
    {
        $alias = self::where('name', $name)->first();
        if ($alias) {
            return $alias;
        }

        return self::create(['unit_id' => $unit_id, 'name' => $name]);
    }
}
