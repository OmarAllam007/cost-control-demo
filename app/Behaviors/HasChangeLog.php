<?php
/**
 * Created by PhpStorm.
 * User: hazem
 * Date: 3/27/17
 * Time: 11:35 AM
 */

namespace App\Behaviors;


trait HasChangeLog
{
    protected static function bootHasChangeLog()
    {
        if (app()->environment() !== 'testing') {
            static::updating(function($model) {
                app()->make('change_log')->record($model);
            });

            static::saved(function($model) {
                app()->make('change_log')->record($model);
            });

            static::deleting(function($model) {
                app()->make('change_log')->record($model, $delete = true);
            });
        }
    }
}