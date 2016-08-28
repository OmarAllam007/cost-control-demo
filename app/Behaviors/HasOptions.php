<?php

namespace App\Behaviors;

use Illuminate\Support\Str;

trait HasOptions
{

    public static function options()
    {
        if (empty(static::$alias)) {
            $alias = Str::title(basename(str_replace('\\', '/', self::class)));
        } else {
            $alias = self::$alias;
        }

        return static::orderBy('name')->pluck('name', 'id')->prepend('Select ' . $alias, '');
    }
}