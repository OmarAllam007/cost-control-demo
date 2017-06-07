<?php

namespace App;

use App\Behaviors\RecordsUser;
use App\Jobs\CreateRevisionForProject;
use Illuminate\Database\Eloquent\Model;

/**
 * @property mixed project_id
 * @property Project project
 */
class BudgetRevision extends Model
{
    use RecordsUser;

    protected $appends = ['url', 'user', 'created_date'];

    protected $fillable = ['name'];

    function project()
    {
        return $this->belongsTo(Project::class);
    }

    function created_by_user()
    {
        return $this->belongsTo(User::class, 'created_by');
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

        self::created(function (self $rev) {
            dispatch(new CreateRevisionForProject($rev));
        });
    }

    protected function getUserAttribute()
    {
        if ($this->is_automatic) {
            return 'System';
        }

        return $this->created_by_user->name;
    }

    protected function getUrlAttribute()
    {
        return $this->url();
    }

    protected function getCreatedDateAttribute()
    {
        return $this->created_at->format('d/m/Y');
    }

    function url()
    {
        return url("/project/{$this->project_id}/revisions/{$this->id}");
    }
}
