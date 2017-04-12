<?php

namespace App;

use App\Behaviors\CachesQueries;
use App\Behaviors\HasChangeLog;
use App\Behaviors\Tree;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ActivityDivision extends Model
{
    use Tree;
    use SoftDeletes, CachesQueries;
    use HasChangeLog;

    protected $fillable = ['code', 'name', 'parent_id'];

    protected $dates = ['created_at', 'updated_at'];

    protected $orderBy = ['code', 'name'];

    public function getLabelAttribute()
    {
        return $this->code . '' . $this->name;

    }

    public function parent()
    {
        return $this->belongsTo(self::class)->withTrashed();
    }

    public function activities()
    {
        return $this->hasMany(StdActivity::class, 'division_id');
    }

    public function scopeAppendActivity(Builder $query)
    {
        $query->with('activities')
            ->with('children.activities')
            ->with('children.children.activities');
    }

    public function getRootAttribute()
    {
        $this->load(['parent', 'parent.parent', 'parent.parent.parent']);
        $parent_ids = [];
        $parents = collect();
        $parent = $this;
        $parents->push($parent);
        $parent_ids [] = $parent->id;

        while ($parent->parent_id && $parent->id != $parent->parent_id) {
            $parent = $parent->parent;
            $parent_ids[] = $parent->id;
        }

        return $parents;
    }

    function isGeneral()
    {
        if (isset($this->root->id)) {
            return $this->root->id == 779;
        }

        return false;
    }
}