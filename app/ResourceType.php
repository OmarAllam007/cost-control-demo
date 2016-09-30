<?php

namespace App;

use App\Behaviors\HasOptions;
use App\Behaviors\Tree;
use Illuminate\Database\Eloquent\Model;

class ResourceType extends Model
{
    use Tree, HasOptions;

    protected $fillable = ['name','parent_id','code'];

    protected $dates = ['created_at', 'updated_at'];

    public function getLabelAttribute()
    {
        return '# '.$this->name  ;
    }

    public function resources()
    {
        return $this->hasMany(Resources::class,'resource_type_id');

    }

    public function getRootAttribute()
    {
        $this->load(['parent', 'parent.parent', 'parent.parent.parent']);
        $parent = $this;
        while ($parent->parent_id && $parent->id != $parent->parent_id) {
            $parent = $parent->parent;
        }

        return $parent;
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