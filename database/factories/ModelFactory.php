<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

$factory->define(App\User::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->safeEmail,
        'password' => bcrypt(str_random(10)),
        'remember_token' => str_random(10),
    ];
});


$factory->define(App\Project::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->text(50),
    ];
});

$factory->define(App\Period::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->text(50),
        'start_date' => \Carbon\Carbon::today()->firstOfMonth(),
        'project_id' => function() {
            return factory('App\Project')->create()->id;
        }
    ];
});

$factory->define(App\WbsLevel::class, function(Faker\Generator $faker) {
    return [
        'project_id' => function() {
            return factory('App\Project')->create()->id;
        },
        'name' => $faker->text(50),
        'parent_id' => 0,
        'code' => $faker->text(10)
    ];
});

$factory->define('App\BreakdownTemplate', function (Faker\Generator $faker) {
    return [
        'name' => $faker->text(50),
        'code' => $faker->text(10)
    ];
});

$factory->define(App\Breakdown::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->text(50),
        'start_date' => \Carbon\Carbon::today()->firstOfMonth(),
        'wbs_id' => function() {
            return factory('App\WbsLevel')->create()->id;
        },
        'project_id' => function() {
            return factory('App\Project')->create()->id;
        },
        'template_id' => function() {
            return factory('App\BreakdownTemplate')->create()->id;
        },
        'cost_account' => function() {
            return factory('App\Survey')->create()->cost_account;
        }
    ];
});


$factory->define(App\BreakdownResource::class, function(Faker\Generator $faker){
    return [
        'breakdown_id' => function() {
            return factory('App\Breakdown')->create()->id;
        },
        'resource_id' => function() {
            return factory('App\Resources')->create()->id;
        },
        'std_activity_resource_id' => function() {
            return factory('App\StdActivityResource')->create()->id;
        },


    ];
});