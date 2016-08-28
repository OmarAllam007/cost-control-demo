<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Productivity extends Model
{
    protected $fillable = ['csi_category_id',
        'unit', 'crew_structure', 'crew_hours', 'crew_equip', 'daily_output',
        'man_hours', 'equip_hours', 'reduction_factor', 'after_reduction', 'source'];

    protected $dates = ['created_at', 'updated_at'];

    public function category()
    {
      return  $this->belongsTo(CSI_category::class,'csi_category_id');
    }
    public function units()
    {
        return  $this->belongsTo(Unit::class,'unit');
    }

    public function productivityAfterReduction(){

       return $this->after_reduction = ($this->reduction_factor * $this->daily_output) + $this->daily_output;
    }

    public function getAfterReductionAttribute(){

        return  $this->daily_output * (1 - $this->reduction_factor);
    }

    public function scopeOptions(Builder $query)
    {
        return $query->orderBy('csi_code')->pluck('csi_code', 'id')->prepend('Select Productivity Reference', '');
    }

}