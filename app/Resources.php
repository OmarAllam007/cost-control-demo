<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Resources extends Model
{
    protected $table = 'resources';
    protected $fillable = ['resource_code','name','rate','unit','waste','business_partner_id','resource_type_id'];
    protected $dates = ['created_at', 'updated_at'];

    public function type()
    {
        return $this->belongsTo(ResourceType::class,'resource_type_id');

    }
    public function partener()
    {
        return $this->belongsTo(BusinessPartner::class,'business_partner_id');
    }
}