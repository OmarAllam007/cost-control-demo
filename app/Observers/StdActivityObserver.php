<?php
/**
 * Created by PhpStorm.
 * User: hazem
 * Date: 18/04/2018
 * Time: 1:06 PM
 */

namespace App\Observers;


class StdActivityObserver
{
    function creating($activity)
    {
        $part1 = substr($activity->id_partial, 0, 2);
        $part2 = substr($activity->id_partial, -2);

        $activity->sap_code_partial = $part1 . '.' . $part2;
    }
}