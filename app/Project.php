<?php

namespace App;

use App\Behaviors\HasOptions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use SoftDeletes, HasOptions;

    protected static $alias = 'Project';

    protected $fillable = ['name', 'description'];

    protected $dates = ['created_at', 'updated_at'];

}