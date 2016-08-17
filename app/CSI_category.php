<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CSI_category extends Model
{
    protected $table = 'csi_categories';
    protected $fillable = ['name'];

    protected $dates = ['created_at', 'updated_at'];
}