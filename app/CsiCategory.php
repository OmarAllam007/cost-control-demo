<?php

namespace App;

use App\Behaviors\HasChangeLog;
use App\Behaviors\HasOptions;
use App\Behaviors\Tree;
use App\Jobs\CacheCsiCategoryTree;
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

    static function boot()
    {
        self::saved(function() {
            \Cache::forget('csi-tree');
            dispatch(new CacheCsiCategoryTree());
        });

        self::deleted(function() {
            \Cache::forget('csi-tree');
            dispatch(new CacheCsiCategoryTree());
        });
    }
}