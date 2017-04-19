<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ActualRevenue extends Model
{
    protected $table='actual_revenue';
    protected $fillable = ['cost_account','value','project_id','period_id','wbs_id', 'boq_id'];

}
