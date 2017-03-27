<?php

namespace App;

use App\Behaviors\HasChangeLog;
use App\Behaviors\HasOptions;
use App\Behaviors\Tree;
use Illuminate\Database\Eloquent\Model;

class BoqDivision extends Model
{
    use Tree , HasOptions;
    use HasChangeLog;

    protected $fillable = ['name','parent_id','code'];

    protected $dates = ['created_at', 'updated_at'];

    public function items()
    {
        return $this->hasMany(Boq::class,'division_id');
    }
}