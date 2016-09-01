<?php

namespace App;

use App\Behaviors\HasOptions;
use App\Behaviors\Tree;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Productivity extends Model
{
    use Tree,HasOptions;
    protected $fillable = ['csi_category_id',
        'unit', 'crew_structure', 'crew_hours', 'crew_equip', 'daily_output',
        'man_hours', 'equip_hours', 'reduction_factor', 'after_reduction', 'source','code'];

    protected $dates = ['created_at', 'updated_at'];

    public function category()
    {
      return  $this->belongsTo(CsiCategory::class,'csi_category_id');
    }
    public function units()
    {
        return  $this->belongsTo(Unit::class,'unit');
    }



    public function productivityAfterReduction(){

       return $this->after_reduction = (1 - $this->reduction_factor) * $this->daily_output;
    }

    public function getAfterReductionAttribute(){

        return  $this->daily_output * (1 - $this->reduction_factor);
    }

    public function scopeOptions(Builder $query)
    {
        return $query->orderBy('csi_code')->pluck('csi_code', 'id')->prepend('Select Productivity Reference', '');
    }

}