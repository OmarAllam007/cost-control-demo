<?php
namespace App\Filter;

class ProductivityFilter extends AbstractFilter {
    protected $fields = ['code','crew_structure'=> 'like','description' => 'like','unit','source'];
}