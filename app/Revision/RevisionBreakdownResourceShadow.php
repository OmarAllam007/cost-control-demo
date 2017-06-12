<?php

namespace App\Revision;

use App\Project;
use App\StdActivity;
use App\WbsLevel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class RevisionBreakdownResourceShadow extends Model
{
    function std_activity()
    {
        return $this->belongsTo(StdActivity::class, 'activity_id');
    }

    function wbs()
    {
        return $this->belongsTo(WbsLevel::class, 'wbs_id');
    }

    function scopeTrendReport(Builder $query, Project $project)
    {
        $fields = ['a.discipline', 'activity', 'resource_name', 'revision_id'];

        $query->join('std_activities as a', 'activity_id', '=', 'a.id')
            ->groupBy($fields)->select($fields)->selectRaw('sum(budget_cost) as cost')
            ->where('project_id', $project->id);

        return $query;
    }
}
