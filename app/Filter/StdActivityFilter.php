<?php

namespace App\Filter;

class StdActivityFilter extends AbstractFilter
{
    protected $fields = ['name' => 'like', 'division_id'];
}