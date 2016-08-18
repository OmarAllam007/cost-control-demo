<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'survey_categories';
    protected $fillable = ['name'];

    protected $dates = ['created_at', 'updated_at'];
}