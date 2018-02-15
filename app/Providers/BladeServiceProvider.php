<?php
/**
 * Created by PhpStorm.
 * User: hazem.mohamed
 * Date: 2/15/2018
 * Time: 1:38 PM
 */

namespace App\Providers;


use Illuminate\Support\ServiceProvider;

class BladeServiceProvider extends ServiceProvider
{
    function boot()
    {
        \Blade::directive('dump', function($expr) {
            return '<?php dump(' . $expr . '); ?>';
        });

        \Blade::directive('dd', function($expr) {
            return '<?php dd(' . $expr . '); ?>';
        });
    }


    public function register()
    {

    }
}