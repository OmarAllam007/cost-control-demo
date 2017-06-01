<?php

namespace App;

use App\Behaviors\RecordsUser;
use Illuminate\Database\Eloquent\Model;

/**
 * @property mixed project_id
 * @property Project project
 */
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

        self::saved(function (self $rev) {
            if ($rev->is_open) {
                $rev->project->revisions()->where('id', '!=', $rev->id)->update(['is_open' => 0]);
            }
        });
    }

}
