<?php

namespace App\Providers;

use Gate;
use Illuminate\Contracts\Auth\Access\Gate as GateContract;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        \App\Project::class => \App\Policies\ProjectPolicy::class,
        \App\BudgetChangeRequest::class => \App\Policies\ChangeRequestPolicy::class
    ];

    /**
     * Register any application authentication / authorization services.
     *
     * @param  \Illuminate\Contracts\Auth\Access\Gate $gate
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::before(function ($user) {
            if ($user->is_admin) {
                return true;
            }
        });

        Gate::define('admin', function ($user) {
            return $user->is_admin;
        });

        Gate::define('read', 'App\Policies\DataPolicy@read');
        Gate::define('write', 'App\Policies\DataPolicy@write');
        Gate::define('delete', 'App\Policies\DataPolicy@delete');

        Gate::define('dashboard', function ($user) {
            return $user->id == 54;
        });

        Gate::define('wipe', function ($user) {
            return in_array(\Auth::user()->email, [
                'hazem.mohamed@alkifah.com',
                'karim.elsharkawy@alkifah.com',
//                'omar.garana@alkifah.com',
            ]);
        });

    }
}
