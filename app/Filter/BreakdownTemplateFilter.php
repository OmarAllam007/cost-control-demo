<?php
/**
 * Created by PhpStorm.
 * User: hazem
 * Date: 9/25/16
 * Time: 8:40 AM
 */

namespace App\Filter;


use Illuminate\Database\Eloquent\Builder;

class BreakdownTemplateFilter extends AbstractFilter
{
    protected $fields = ['name' => 'like'];

    protected function resource_id($id)
    {
        $this->query->whereRaw('id in (select template_id from std_activity_resources where resource_id = ?)', [$id]);
    }
}