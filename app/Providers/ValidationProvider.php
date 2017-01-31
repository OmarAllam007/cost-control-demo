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
            $request = request();
            $query = Boq::query()->where('wbs_id', request('wbs_id'))->where('cost_account', $value);
            if ($request->route()->hasParameter('boq')) {
                $query->where('id', '!=', $request->route('boq')->id);
            }

            return !$query->exists();
        });
        
        \Validator::replacer('gte', function ($message, $attribute, $rule, $parameters) {
            return str_replace(':gte', $parameters[0], $message);
        });

        \Validator::replacer('lte', function ($message, $attribute, $rule, $parameters) {
            return str_replace(':lte', $parameters[0], $message);
        });

        \Validator::replacer('gt', function ($message, $attribute, $rule, $parameters) {
            return str_replace(':gt', $parameters[0], $message);
        });

        \Validator::replacer('lt', function ($message, $attribute, $rule, $parameters) {
            return str_replace(':lt', $parameters[0], $message);
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
