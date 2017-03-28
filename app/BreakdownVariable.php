<?php

namespace App;

use App\Behaviors\HasChangeLog;
use Illuminate\Database\Eloquent\Model;

class BreakdownVariable extends Model
{
    use HasChangeLog;

    protected $fillable = ['name', 'value', 'display_order', 'qty_survey_id', 'breakdown_id'];

    function survey(){
        return $this->belongsTo(Survey::class,'qty_survey_id');
    }

    function breakdown(){
        return $this->belongsTo(Breakdown::class,'breakdown_id');
    }
}
