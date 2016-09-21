<?php
/**
 * Created by PhpStorm.
 * User: hazem
 * Date: 9/20/16
 * Time: 4:41 PM
 */

namespace App\Filter;


use Illuminate\Database\Eloquent\Builder;

abstract class AbstractFilter
{

    /**
     * @var Builder
     */
    protected $query;

    /**
     * @var array
     */
    protected $filters;

    protected $fields = [];

    function __construct(Builder $query, $filters = [])
    {
        $this->query = $query;
        $this->filters = $filters;
    }

    /**
     * @return Builder
     */
    function filter()
    {
        if ($this->filters) {
            foreach ($this->filters as $field => $value) {
                if (!$value) {
                    continue;
                }

                call_user_func_array([$this, $field], [$value]);
            }
        }

        return $this->query;
    }

    function __call($name, $arguments)
    {
        if (in_array($name, $this->fields)) {
            $this->query->where($name, $arguments[0]);
        }

        if (isset($this->fields[$name]) && $this->fields[$name] == 'like') {
            $this->query->where($name, 'like', "%{$arguments[0]}%");
        }
    }
}