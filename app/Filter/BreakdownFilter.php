<?php
/**
 * Created by PhpStorm.
 * User: hazem
 * Date: 9/25/16
 * Time: 2:52 PM
 */

namespace App\Filter;


use App\ResourceType;
use Illuminate\Database\Eloquent\Builder;

class BreakdownFilter extends AbstractFilter
{
    protected function wbs_id($id)
    {
        $this->query->whereHas('breakdown', function(Builder $q) use ($id) {
            $q->where('wbs_level_id', $id);
        });
    }

    protected function activity($id)
    {
        $this->query->whereHas('breakdown', function(Builder $q) use ($id) {
            $q->where('std_activity_id', $id);
        });
    }

    protected function cost_account($value)
    {
        $this->query->whereHas('breakdown', function(Builder $q) use ($value) {
            $q->where('cost_account', 'LIKE', "%$value%");
        });
    }

    protected function resource($name)
    {
        $this->query->whereHas('template_resource.resource', function(Builder $q) use ($name) {
            $q->where('name', 'LIKE', "%$name%");
        });
    }

    protected function resource_type($id)
    {
        $this->query->whereHas('resource.resource', function(Builder $q) use ($id) {
            $type = ResourceType::with(['children', 'children.children'])->find($id);
            $ids = $type->getChildrenIds();
            $q->whereIn('resource_type_id', $ids);
        });
    }
}