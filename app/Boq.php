<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Boq extends Model
{
    protected $fillable = [
        'wbs_id','item','description','type','unit_id','quantity','dry_ur','price_ur','arabic_description'
        ,'kcc_qty','subcon','materials','manpower','cost_account','item_code','division_id','project_id'
    ];

    protected $dates = ['created_at', 'updated_at'];

    public function unit()
    {
        return $this->belongsTo(Unit::class,'unit_id');
    }

    public function getAllQuantity($quantity)
    {
        return $quantity*16 ;
    }

    public function getDryForBuilding($quantity,$dry)
    {
        return $dry*$quantity;
    }

    public function getPriceForBuilding($price,$quantity)
    {
        return $price*$quantity;
    }

    public function getDryForAllBuilding($quantity,$dry)
    {
        return $dry*$quantity*16;
    }

    public function getPriceForAllBuilding($quantity,$price)
    {
        return $price*$quantity*16;
    }




}