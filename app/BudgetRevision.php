<?php

namespace App;

use App\Behaviors\RecordsUser;
use App\Jobs\CreateRevisionForProject;
use App\Revision\RevisionBreakdownResourceShadow;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

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

        self::deleted(function ($revision) {
            $tables = [
                'revision_boqs', 'revision_breakdown_resource_shadows', 'revision_breakdown_resources',
                'revision_breakdowns', 'revision_productivities', 'revision_qty_surveys', 'revision_resources'
            ];

            foreach ($tables as $table) {
                \DB::table($table)->where('revision_id', $revision->id)->delete();
            }
        });
    }

    /**
     * @return Collection
     */
    public function statsByDiscipline()
    {
        return RevisionBreakdownResourceShadow::join('std_activities as a', 'activity_id', '=', 'a.id')
            ->groupBy('a.discipline')->orderBy('a.discipline')->selectRaw('a.discipline as discipline, sum(budget_cost) as cost')
            ->where('revision_id', $this->id)
            ->get()->keyBy('discipline');
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
