<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CostConcerns extends Model
{
        protected $table = 'cost_concerns';
        protected $fillable = ['report_name','data','project_id','period_id','comment'];
}
