<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Productivity extends Model
{
    protected $fillable = ['csi_code', 'csi_category_id', 'description',
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
}