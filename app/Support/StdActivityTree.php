<?php
/**
 * Created by PhpStorm.
 * User: hazem
 * Date: 11/2/17
 * Time: 12:19 PM
 */

namespace App\Support;


use App\ActivityDivision;
use Illuminate\Support\Collection;

class StdActivityTree
{
    /** @var Collection */
    protected $divisions;

    function get()
    {
        $this->divisions = ActivityDivision::orderBy('name')->with('activities')->get()->groupBy('parent_id');

        return $this->buildTree();
    }

    protected function buildTree($parent = 0)
    {
        return $this->divisions->get($parent, collect())->map(function ($division) {
            $division->subtree = $this->buildTree($division->id);
            return $division;
        });
    }
}