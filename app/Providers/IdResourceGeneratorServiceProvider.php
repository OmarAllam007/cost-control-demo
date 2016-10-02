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
            $names = explode('»', $resource->types->path);
            $code = [];
            foreach ($names as $name) {
                $name = trim($name);
                $code[] = substr(trim($name), 0, 1);
                if (strrchr($name, ' ')) {
                    $position = strrpos($name, ' ');
                    $code[] = substr($name, $position + 1, 1);
                }
                $code[] = '.';
            }
            $code = implode('', $code);

            $num = 1;
            $item = Resources::where('resource_code', 'like', $code . '_')->get(['resource_code'])->last();

            if (!is_null($item)) {
                $itemCode = substr($item->resource_code, strrpos($item->resource_code, '.') + 1);
                $itemCode++;
                $code = $code . $itemCode;
                $resource->resource_code = $code;
            } else {
                $resource->resource_code = $code . $num;
            }

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
