<?php

namespace App;

use App\Behaviors\HasOptions;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasOptions;

    protected $table = 'survey_categories';
    protected $fillable = ['name'];

    protected $dates = ['created_at', 'updated_at'];
}