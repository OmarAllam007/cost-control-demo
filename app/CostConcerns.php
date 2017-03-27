<?php

namespace App;

use App\Behaviors\HasChangeLog;
use Illuminate\Database\Eloquent\Model;

class CostConcerns extends Model
{
    use HasChangeLog;

    protected $table = 'cost_concerns';
    protected $fillable = ['report_name', 'data', 'project_id', 'period_id', 'comment'];
}
