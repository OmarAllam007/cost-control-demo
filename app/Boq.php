<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Boq extends Model
{
    protected $fillable = [
        'wbs_id','item','description','type','unit_id','quantity','dry_ur','price_ur','arabic_description'
    ];

    protected $dates = ['created_at', 'updated_at'];

    public function unit()
    {
        return $this->belongsTo(Unit::class,'unit_id');
    }
}