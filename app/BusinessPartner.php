<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BusinessPartner extends Model
{
    protected $fillable = ['name','type'];

    protected $dates = ['created_at', 'updated_at'];

}