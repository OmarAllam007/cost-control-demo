<?php

namespace App\Filter;

use App\ResourceType;

class ResourcesFilter extends AbstractFilter
{
    protected $fields = ['unit', 'name' => 'like', 'resource_type_id','resource_code'];

    function resource_type_id($id)
    {
        $type = ResourceType::with(['children', 'children.children', 'children.children.children'])
            ->find($id);

        $ids = $type->getChildrenIds();

        $this->query->whereIn('resource_type_id', $ids);
    }
}