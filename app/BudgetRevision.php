<?php

namespace App;

use App\Behaviors\RecordsUser;
use Illuminate\Database\Eloquent\Model;

class BudgetRevision extends Model
{
    use RecordsUser;

    protected $fillable = ['name'];

    function project()
    {
        return $this->belongsTo(Project::class);
    }

    protected static function boot()
    {
        parent::boot();

        self::creating(function (self $rev) {
            $lastRevNum = 0;

            $lastRev = self::where('project_id', $rev->project_id)->latest()->first();
            if ($lastRev) {
                $lastRevNum = $lastRev->rev_num;
            }

            $rev->rev_num = $lastRevNum + 1;
        });
    }

}
