<?php

namespace App\Providers;

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
        \App\Project::class => \App\Policies\ProjectPolicy::class
    ];

    /**
     * Register any application authentication / authorization services.
     *
     * @param  \Illuminate\Contracts\Auth\Access\Gate  $gate
     * @return void
     */
    public function boot(GateContract $gate)
    {
        $this->registerPolicies($gate);

        $gate->before(function ($user) {
            if ($user->is_admin) {
                return true;
            }
        });
        $gate->define('read', 'App\Policies\DataPolicy@read');
        $gate->define('write', 'App\Policies\DataPolicy@write');
        $gate->define('delete', 'App\Policies\DataPolicy@delete');

        $gate->define('wipe', function($user) {
            return in_array(\Auth::user()->email, [
                'hazem.mohamed@alkifah.com',
                'karim.elsharkawy@alkifah.com',
//                'omar.garana@alkifah.com',
            ]);
        });
    }
}
