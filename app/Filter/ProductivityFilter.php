<?php
namespace App\Filter;

use App\CsiCategory;

class ProductivityFilter extends AbstractFilter {
    protected $fields = ['csi_category_id','code'=>'like','description' => 'like','source'];

    function csi_category_id($id)
    {
        $type = CsiCategory::with(['children', 'children.children', 'children.children.children'])
            ->find($id);

        $ids = $this->getCsiCategory($type);

        $this->query->whereIn('csi_category_id', $ids);
    }
    /**
     * @param $type
     * @return \Illuminate\Support\Collection
     */
    protected function getCsiCategory($type)
    {
        $ids = collect($type->id);
        foreach ($type->children as $child) {
            $subids = $this->getCsiCategory($child);
            $ids = $ids->merge($subids);
        }
        return $ids;
    }
}