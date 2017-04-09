<?php

namespace App;

use App\Behaviors\HasChangeLog;
use Illuminate\Database\Eloquent\Model;

class LaborTrendUploadTable extends Model
{
    use HasChangeLog;

    protected $table='labor_trend_upload_table';
    protected $fillable = ['uploaded_by','file_path','period_id','project_id'];
}
