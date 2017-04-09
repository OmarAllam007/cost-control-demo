<?php

namespace App;

use App\Behaviors\HasChangeLog;
use App\Behaviors\HasOptions;
use App\Behaviors\Tree;
use Illuminate\Database\Eloquent\Model;

class CsiCategory extends Model
{
    use Tree,HasOptions;
    use HasChangeLog;

    protected $table = 'csi_categories';
    protected $fillable = ['name','parent_id','code'];

    protected $dates = ['created_at', 'updated_at'];

    public function productivity()
    {
        return $this->hasMany(Productivity::class,'csi_category_id');
    }
}