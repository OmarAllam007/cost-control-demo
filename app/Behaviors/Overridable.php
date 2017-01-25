<?php
/**
 * Created by PhpStorm.
 * User: hazem
 * Date: 9/28/16
 * Time: 9:28 AM
 */

namespace App\Behaviors;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

trait Overridable
{
    function hasVersionFor($project_id)
    {
        return self::version($project_id, $this->id)->exists();
    }

    function versionFor($project_id)
    {
        if ($this->hasVersionFor($project_id)) {
            return self::version($project_id, $this->id)->first();
        }

        return $this;
    }

    function scopeVersion(Builder $query, $project_id, $original_id)
    {
        $field = Str::singular($this->getTable()) . '_id';
        $query->where($field, $original_id)
            ->where('project_id', $project_id);
    }

    function scopeBasic(Builder $query)
    {
        $query->where(function (Builder $query) {
            $query->where('project_id', 0)
                ->orWhereNull('project_id');
        });
    }
}