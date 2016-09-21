<?php

namespace App\Filter;

class BusinessPartnerFilter extends AbstractFilter
{
    protected $fields = ['name' => 'like','type'];
}