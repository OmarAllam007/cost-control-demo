<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LaborTrendUploadTable extends Model
{
    protected $table='labor_trend_upload_table';
    protected $fillable = ['uploaded_by','file_path','period_id','project_id'];
}
