<?php
/**
 * Created by PhpStorm.
 * User: hazem
 * Date: 11/7/16
 * Time: 3:08 PM
 */

namespace App\Behaviors;

use App\Support\CachableBuilder;
use Illuminate\Database\Eloquent\Builder;
use App\Support\CachableQuery;

trait CachesQueries
{
    protected function newBaseQueryBuilder()
    {
        $conn = $this->getConnection();

        $grammar = $conn->getQueryGrammar();

        return new CachableQuery($conn, $grammar, $conn->getPostProcessor());
    }
}