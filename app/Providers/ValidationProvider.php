<?php

namespace App\Providers;

use App\Boq;
use App\Survey;
use App\WbsLevel;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Validator;

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

        \Validator::extend('boq_unique', function($attribute, $value, $options, Validator $validator) {
            $data = $validator->getData();
            $query = Boq::query()->where('wbs_id', $data['wbs_id'])->where($attribute, $value);

            $request = request();
            if ($request->route()->hasParameter('boq')) {
                $query->where('id', '!=', $request->route('boq')->id);
            }

            return !$query->exists();
        });

        \Validator::extend('qs_code_unique', function($attribute, $value, $options, Validator $validator) {
            $data = $validator->getData();
            $query = Survey::where('wbs_id', $data['wbs_id'])->where($attribute, $value);

            $request = request();
            if ($request->route()->hasParameter('survey')) {
                $query->where('id', '!=', $request->route('survey')->id);
            }

            return !$query->exists();
        });

        \Validator::extend('qs_has_boq', function($attribute, $value, $options, Validator $validator) {
            $data = $validator->getData();
            $wbs = WbsLevel::find($data['wbs_level_id']);
            if (!$wbs) {
                return false;
            }
            return Boq::whereIn('wbs_id', $wbs->getParentIds())->where('item_code', $value)->exists();
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
