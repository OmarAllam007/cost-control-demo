<?php

namespace App\Support;

use App\CsiCategory;
use Illuminate\Support\Collection;

class ProductivityCategoriesTree
{
    /** @var Collection */
    private $categories;

    /** @var Collection */
    private $tree;

    function get()
    {
        $this->categories = CsiCategory::orderBy('name')->get()->groupBy('parent_id');

        return $this->tree();
    }

    function tree($parent_id = 0)
    {
        return $this->categories->get($parent_id, collect())->map(function($category) {
            $category->subtree = $this->tree($category->id);
            return $category;
        });
    }
}