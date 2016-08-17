<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Resources extends Model
{
    protected $table = 'resources';
    protected $fillable = ['resource_code','name','rate','unit','waste','business_partner','resource_type'];
    protected $dates = ['created_at', 'updated_at'];

    public  function businessParteners()
    {
        return $this->belongsTo('App\BusinessPartner','business_partner');
    }

}