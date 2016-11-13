<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FinancialPeriod extends Model
{
    protected $fillable = ['project_id','start_date', 'end_date', 'open', 'opened_time', 'closed_time', 'description'];

    public function project(){
        return $this->belongsTo(Project::class);
    }

}
