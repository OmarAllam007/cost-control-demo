<?php
/**
 * Created by PhpStorm.
 * User: Hazem Mohamed
 * Date: 5/18/17
 * Time: 1:03 PM
 */

namespace App\Behaviors;

use Illuminate\Database\Eloquent\Model;

trait RecordsUser
{
    static function bootRecordsUser()
    {
        self::creating(function (Model $model) {
            $model->created_by = \Auth::id() ?: 2;
        });

        self::saving(function (Model $model) {
            $model->updated_by = \Auth::id() ?: 2;
        });
    }
}