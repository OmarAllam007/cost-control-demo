<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Survey extends Model
{
    protected $table = 'qty_surveys';
    protected $fillable = ['unit_id','budget_qty','eng_qty','cost_account','category_id','description'];
    protected $dates = ['created_at', 'updated_at'];

    public function categories()
    {
        return $this->belongsTo(Category::class,'category_id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class,'unit_id');
    }
}