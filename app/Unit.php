<?php

namespace App;

use App\Behaviors\CachesQueries;
use App\Behaviors\HasChangeLog;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use CachesQueries;
    use HasChangeLog;

    protected $table = 'units';
    protected $fillable = ['type','code'];

    protected $dates = ['created_at', 'updated_at'];

    static function options()
    {
        return static::orderBy('type')->pluck('type', 'id')->prepend('Select Unit', '');
    }
}