<?php
/**
 * Created by PhpStorm.
 * User: hazem
 * Date: 11/7/16
 * Time: 3:10 PM
 */

namespace App\Support;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;

class CachableBuilder extends Builder
{

    public function getModels($columns = ['*'])
    {
        $results = $this->query->get($columns);

        $connection = $this->model->getConnectionName();

        return $this->model->hydrate($results, $connection)->all();
    }


}