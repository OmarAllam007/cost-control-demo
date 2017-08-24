<?php
/**
 * Created by PhpStorm.
 * User: hazem
 * Date: 8/20/17
 * Time: 5:05 PM
 */

namespace App\Observers;


use App\Jobs\CacheResourcesTree;
use App\ResourceType;

class ResourceTypeObserver
{
    function creating(ResourceType $type)
    {
        if (empty($type->code)) {
            $type->code = $this->generateCode($type);
        }
    }

    function saved()
    {

        dispatch(new CacheResourcesTree());
    }

    function deleted()
    {
        \Cache::forget('resources-tree');
        dispatch(new CacheResourcesTree());
    }

    protected function generateCode($type)
    {
        $lastType = ResourceType::where('parent_id', $type->parent_id)->max('code');

        if ($lastType) {
            $tokens = explode('.', $lastType);
            $last = count($tokens) - 1;
            $length = strlen($tokens[$last]);
            $tokens[$last] = sprintf("%0{$length}d", $tokens[$last] + 1);
            return implode('.', $tokens);
        }

        return $type->parent->code . '.001';
    }
}