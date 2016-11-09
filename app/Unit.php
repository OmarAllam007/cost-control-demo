<?php

namespace App;

use App\Behaviors\CachesQueries;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use CachesQueries;

    protected $table = 'units';
    protected $fillable = ['type','code'];

    protected $dates = ['created_at', 'updated_at'];

    static function options()
    {
        return static::orderBy('type')->pluck('type', 'id')->prepend('Select Unit', '');
    }
}