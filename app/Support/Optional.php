<?php
/**
 * Created by PhpStorm.
 * User: hazem
 * Date: 28/12/17
 * Time: 1:41 PM
 */

namespace App\Support;


class Optional
{
    private $obj;

    public function __construct($obj = null)
    {
        $this->obj = $obj;
    }

    public function __get($name)
    {
        if (!is_null($this->obj) && is_object($this->obj)) {
            return $this->obj->$name;
        }

        return null;
    }

    public function __call($name, $arguments)
    {
        if (!is_null($this->obj) && is_object($this->obj)) {
            return call_user_func_array([$this->obj, $name], $arguments);
        }

        return null;
    }
}