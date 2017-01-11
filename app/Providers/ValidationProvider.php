<?php

namespace App\Providers;

use App\Boq;
use Illuminate\Support\ServiceProvider;

class ValidationProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        \Validator::extend('gt', function($attribute, $value, $parameters) {
            return $value > $parameters[0];
        });

        \Validator::extend('gte', function($attribute, $value, $parameters) {
            return $value >= $parameters[0];
        });

        \Validator::extend('lt', function($attribute, $value, $parameters) {
            return $value < $parameters[0];
        });

        \Validator::extend('lte', function($attribute, $value, $parameters) {
            return $value <= $parameters[0];
        });

        \Validator::extend('boq_unique', function($attribute, $value) {
            return !Boq::where('wbs_id', request('wbs_id'))->where('cost_account', $value)->exists();
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {

    }
}
