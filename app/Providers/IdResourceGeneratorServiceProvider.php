<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Resources;

class IdResourceGeneratorServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {

        Resources::creating(function (Resources $resource) {
            //get first character of root
            $rootName = substr($resource->types->root->name, strpos($resource->types->root->name, '.') + 1, 1);

            $names = explode('»', $resource->types->path);
            $code = [];
            $code [] = $rootName;
            //if Labors get by letter else by number
            if ($rootName != 'L') {
                foreach ($names as $key => $name) {
                    if ($key == 0) {
                        continue;
                    }

                    $name = trim($name);
                    $divname = substr($name, 0, strpos($resource->types->root->name, '.'));
                    $code [] = $divname;

                }
            } else {
                foreach ($names as $key => $name) {
                    if ($key == 0) {
                        continue;
                    }
                    $name = trim($name);
                    $divname = substr($name, strpos($resource->types->root->name, '.') + 1, 1);
                    $code [] = $divname;

                }
            }

            $resourceNumber = Resources::where('resource_type_id', $resource->types->id)->count();
            $resourceNumber++;
            $code[] = $resourceNumber <= 10 ? '0' . $resourceNumber : $resourceNumber;
            $finalCode = implode('.', $code);

            $resource->resource_code = $finalCode;
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        echo '';
    }
}
