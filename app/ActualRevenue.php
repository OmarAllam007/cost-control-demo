<?php

namespace App;

use App\Behaviors\RecordsUser;
use Illuminate\Database\Eloquent\Model;

class ActualRevenue extends Model
{
    use RecordsUser;

    protected $table='actual_revenue';
    protected $fillable = ['cost_account','value','project_id','period_id','wbs_id', 'boq_id'];

}
