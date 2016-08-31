<?php

namespace App;

use App\Behaviors\HasOptions;
use App\Behaviors\Tree;
use Illuminate\Database\Eloquent\Model;

class CsiCategory extends Model
{
    use Tree,HasOptions;
    protected $table = 'csi_categories';
    protected $fillable = ['name','parent_id'];

    protected $dates = ['created_at', 'updated_at'];

}