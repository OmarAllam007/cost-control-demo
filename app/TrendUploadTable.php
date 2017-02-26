<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TrendUploadTable extends Model
{
    protected $table='trend_upload_table';
    protected $fillable = ['uploaded_by','file_path','period_id','project_id'];
}
