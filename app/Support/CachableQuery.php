<?php

namespace App\Support;


use Illuminate\Database\Query\Builder;

class CachableQuery extends Builder
{
    protected static $cached = [];

    protected function runSelect()
    {
        $sql = strtolower($this->toSql());
        $binds = strtolower(json_encode($this->getBindings()));
        $code = sha1($sql.$binds);
        if (isset(static::$cached[$code])) {
            return static::$cached[$code];
        }

        static::$cached[$code] = parent::runSelect();
        return static::$cached[$code];
    }
}