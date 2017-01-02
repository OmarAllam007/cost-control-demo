<?php

namespace App\Providers;

use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\SomeEvent' => [
            'App\Listeners\EventListener',
        ],
    ];

    /**
     * Register any other events for your application.
     *
     * @param  \Illuminate\Contracts\Events\Dispatcher  $events
     * @return void
     */
    public function boot(DispatcherContract $events)
    {
        parent::boot($events);

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
    }
}
