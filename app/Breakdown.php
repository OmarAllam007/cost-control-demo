<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Breakdown extends Model
{
    protected $fillable = ['activity_id', 'breakdown_template_id', 'name', 'cost_account'];
}
