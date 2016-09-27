<?php
/**
 * Created by PhpStorm.
 * User: omar.garana
 * Date: 9/27/2016
 * Time: 12:02 PM
 */

namespace App;


use Illuminate\Database\Eloquent\Model;

class ProductivityList extends Model
{
    protected $table = 'productivity_list';
    protected $fillable = ['name','type','discipline'];
}