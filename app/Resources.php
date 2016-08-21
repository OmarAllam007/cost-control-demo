<?php

namespace App;

use App\Behaviors\HasOptions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Resources extends Model
{
    use SoftDeletes, HasOptions;

    protected $table = 'resources';
    protected $fillable = ['resource_code','name','rate','unit','waste','business_partner_id','resource_type_id'];
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