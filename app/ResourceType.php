<?php

namespace App;

use App\Behaviors\CachesQueries;
use App\Behaviors\HasChangeLog;
use App\Behaviors\HasOptions;
use App\Behaviors\Tree;
use Illuminate\Database\Eloquent\Model;

class ResourceType extends Model
{
    use Tree, HasOptions, CachesQueries;
    use HasChangeLog;

    protected $fillable = ['name', 'parent_id', 'code'];

    protected $dates = ['created_at', 'updated_at'];

    protected $orderBy = ['code', 'name'];

    protected $root;

    public function getLabelAttribute()
    {
        return '# ' . $this->name;
    }

    public function resources()
    {
        return $this->hasMany(Resources::class, 'resource_type_id');

    }

    public function getRootAttribute()
    {
        if ($this->root) {
            return $this->root;
        }

        $this->load(['parent', 'parent.parent', 'parent.parent.parent']);
        if (!$this->parent_id) {
            return $this;
        }
        $parent = $this->parent;
        if (!$this->parent) {
            dd($this->getAttributes());
        }
        while ($parent->parent_id && $parent->id != $parent->parent_id) {
            if (!$parent->parent) {
                return $this->root = $parent;
            }
            $parent = $parent->parent;
        }

        return $this->root = $parent;
    }

    function getChildrenIds()
    {
        $ids = collect($this->id);

        /** @var ResourceType $child */
        foreach ($this->children as $child) {
            $subids = $child->getChildrenIds();
            $ids = $ids->merge($subids);
        }

        return $ids;
    }

}