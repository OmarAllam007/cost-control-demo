<?php

namespace App;

use App\Behaviors\HasChangeLog;
use App\Behaviors\HasOptions;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasOptions;

    use HasChangeLog;

    protected $table = 'survey_categories';
    protected $fillable = ['name','code'];

    protected $dates = ['created_at', 'updated_at'];
}