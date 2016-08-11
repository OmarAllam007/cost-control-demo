<?php

namespace Make;

use Illuminate\Support\ServiceProvider;

class MakeServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->app['command.make.scaffold'] = app()->share(function() {
            return new Console\Command\Scaffold();
        });

        $this->commands([
            'command.make.scaffold'
        ]);
    }
}