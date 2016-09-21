<?php

namespace App\Filter;

use App\ResourceType;

class ResourcesFilter extends AbstractFilter
{
    protected $fields = ['unit', 'name' => 'like', 'resource_type_id'];

    function resource_type_id($id)
    {
        $type = ResourceType::with(['children', 'children.children', 'children.children.children'])
            ->find($id);

        $ids = $this->getTypeChildren($type);

        $this->query->whereIn('resource_type_id', $ids);
    }

    /**
     * @param $type
     * @return \Illuminate\Support\Collection
     */
    protected function getTypeChildren($type)
    {
        $ids = collect($type->id);

        foreach ($type->children as $child) {
            $subids = $this->getTypeChildren($child);
            $ids = $ids->merge($subids);
        }

        return $ids;
    }
}