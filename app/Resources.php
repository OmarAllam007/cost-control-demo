<?php

namespace App;

use App\Behaviors\HasOptions;
use App\Behaviors\Tree;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Resources extends Model
{
    use SoftDeletes, HasOptions,Tree;


    protected $table = 'resources';
    protected $fillable = ['resource_code','name','rate','unit','waste','business_partner_id','resource_type_id','reference'];
    protected $dates = ['created_at', 'updated_at'];

    public function types()
    {
        return $this->belongsTo(ResourceType::class,'resource_type_id');

    }
    public function parteners()
    {
        return $this->belongsTo(BusinessPartner::class,'business_partner_id');
    }

    public function units(){
        return $this->belongsTo(Unit::class,'unit');
    }


}