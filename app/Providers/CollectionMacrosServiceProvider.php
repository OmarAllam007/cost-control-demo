<?php

namespace App\Providers;

use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;

class CollectionMacrosServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        Collection::macro('mergeWithKeys', function($newCollection) {
            $result = collect($this->toArray());

            foreach ($newCollection as $key => $val) {
                $result->put($key, $val);
            }

            return $result;
        });

        Collection::macro('sortByKeys', function() {
            $keys = $this->keys()->sort();
            $newCollection = collect();
            $keys->each(function($key) use ($newCollection) {
                $newCollection->put($key, $this->get($key));
            });

            return $newCollection;
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
