<?php

namespace App;

use App\Behaviors\HasOptions;
use App\Behaviors\Tree;
use Illuminate\Database\Eloquent\Model;

class BusinessPartner extends Model
{
    use HasOptions,Tree;

    protected static $alias = 'Business Partner';

    protected $fillable = ['name','type','code'];

    protected $dates = ['created_at', 'updated_at'];

}