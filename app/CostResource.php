<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CostResource extends Model
{
    protected $fillable = ['resource_id', 'period_id', 'rate'];
}
