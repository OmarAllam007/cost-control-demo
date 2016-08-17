<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Survey extends Model
{
    protected $table = 'qty_surveys';
    protected $fillable = ['unit_id','budget_qty','eng_qty','cost_name','cost_account','category_id'];
    protected $dates = ['created_at', 'updated_at'];

    public function categories()
    {

        $this->belongsTo(Category::class);
    }
}