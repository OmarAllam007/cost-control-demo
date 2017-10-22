<?php

namespace App\Providers;

use App\Resources;
use App\ResourceType;
use Illuminate\Support\ServiceProvider;

class ResourceRulesProvider extends ServiceProvider
{
    public function boot()
    {
        \Validator::extend('no_children_on_leaf', function ($attribute, $value) {
            return ! Resources::where('resource_type_id', $value)->exists();
        });

        \Validator::extend('no_resource_on_parent', function ($attribute, $value) {
            return ! ResourceType::where('parent_id', $value)->exists();
        });

        \Validator::extend('unique_name', function ($attribute, $value) {
            $query = Resources::query()->whereRaw("REPLACE(name, ' ', '') = ?", [str_replace(' ', '', $value)]);

            $resource = request()->route('resource');
            if ($resource) {
                $query->where('id', '!=', $resource->id);
            }

            return ! $query->exists();
        });
    }

    public function register() {}
}
