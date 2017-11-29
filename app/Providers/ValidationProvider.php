<?php

namespace App\Providers;

use App\Boq;
use App\Project;
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
        $this->numberValidators();

        $this->boqValidators();

        $this->qsValidators();

        $this->replacers();
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {

    }

    private function numberValidators()
    {
        \Validator::extend('gt', function ($attribute, $value, $parameters) {
            return $value > $parameters[0];
        });

        \Validator::extend('gte', function ($attribute, $value, $parameters) {
            return $value >= $parameters[0];
        });

        \Validator::extend('lt', function ($attribute, $value, $parameters) {
            return $value < $parameters[0];
        });

        \Validator::extend('lte', function ($attribute, $value, $parameters) {
            return $value <= $parameters[0];
        });
    }

    private function boqValidators()
    {
        \Validator::extend('boq_unique', function ($attribute, $value, $options, Validator $validator) {
            $data = $validator->getData();
            $query = Boq::query()->where('wbs_id', $data['wbs_id'])->where($attribute, $value);

            $request = request();
            if ($request->route()->hasParameter('boq')) {
                $query->where('id', '!=', $request->route('boq')->id);
            }

            return !$query->exists();
        });
    }

    private function qsValidators()
    {
        \Validator::extend('qs_code_unique', function ($attribute, $value, $options, Validator $validator) {
            $data = $validator->getData();
            $query = Survey::where('wbs_level_id', $data['wbs_level_id'])->where($attribute, $value);

            $request = request();
            if ($request->route()->hasParameter('survey')) {
                $query->where('id', '!=', $request->route('survey')->id);
            }

            return !$query->exists();
        });

        \Validator::extend('qs_has_boq', function ($attribute, $value, $options, Validator $validator) {
            $data = $validator->getData();
            $wbs = WbsLevel::find($data['wbs_level_id']);
            if (!$wbs) {
                return false;
            }
            return Boq::whereIn('wbs_id', $wbs->getParentIds())->where('item_code', $value)->exists();
        });

        \Validator::extend('has_copy_permission', function ($attribute, $project_id) {
            $project = Project::find($project_id);
            return can('wbs', $project) && can('breakdown', $project) &&
                can('resources', $project) && can('breakdown_templates', $project) &&
                can('productivity', $project);
        });


        \Validator::extend('qs_code_found_on_wbs', function ($attribute, $value, $options, Validator $validator) {
            $data = $validator->getData();
            $wbs = WbsLevel::find($data['wbs_level_id']);

            if (!$wbs) {
                return false;
            }

            return Survey::whereIn('wbs_id', $wbs->getParentIds())->where('qs_code', $value)->exists();
        });
    }

    private function replacers()
    {
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
}
