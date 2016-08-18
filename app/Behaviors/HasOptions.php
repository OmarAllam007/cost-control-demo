<?php

namespace App\Behaviors;

use Illuminate\Support\Str;

trait HasOptions
{

    public static function options()
    {
        if (empty(self::$alias)) {
            $alias = Str::title(basename(str_replace('\\', '/', self::class)));
        } else {
            $alias = self::$alias;
        }

        return self::orderBy('name')->pluck('name', 'id')->prepend('Select ' . $alias, '');
    }
}