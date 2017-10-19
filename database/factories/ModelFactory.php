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
        'is_admin' => false,
    ];
});


$factory->define(App\Project::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->text(50),
        'project_code' => $faker->slug(1),
        'owner_id' => function() {
            return factory('App\User')->create()->id;
        },
        'cost_owner_id' => function() {
            return factory('App\User')->create()->id;
        },
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
        'code' => $faker->slug(1)
    ];
});

$factory->define('App\BreakdownTemplate', function (Faker\Generator $faker) {
    return [
        'name' => $faker->text(50),
        'code' => $faker->text(10),
        'std_activity_id' => function () {
            return factory(App\StdActivity::class)->create()->id;
        }
    ];
});

$factory->define(App\Breakdown::class, function (Faker\Generator $faker) {
    $template = factory('App\BreakdownTemplate')->create();

    return [
        'project_id' => function() {
            return factory('App\Project')->create()->id;
        },
        'wbs_level_id' => function() {
            return factory('App\WbsLevel')->create()->id;
        },
        'template_id' => $template->id,
        'std_activity_id' => $template->std_activity_id,
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
        }
    ];
});

$factory->define(App\ActivityDivision::class, function(Faker\Generator $faker){
    return [
        'name' => $faker->text(50),
        'code' => $faker->slug(1),
        'parent_id' => 0
    ];
});

$factory->define(App\StdActivity::class, function(Faker\Generator $faker){
    return [
        'name' => $faker->text(50),
        'code' => $faker->slug(1),
        'id_partial' => $faker->text(7),
        'discipline' => $faker->text(10),
        'division_id' => function() {
            return factory('App\ActivityDivision')->create()->id;
        }
    ];
});

$factory->define(App\Survey::class, function (Faker\Generator $faker) {
    return [
        'project_id' => function() {
            return factory('App\Project')->create()->id;
        },
        'wbs_level_id' => function() {
            return factory('App\WbsLevel')->create()->id;
        },
        'cost_account' => $faker->slug(5),
        'budget_qty' => $faker->numberBetween(1, 99),
        'eng_qty' => $faker->numberBetween(1, 99),
        'discipline' => $faker->slug(1)
    ];
});

$factory->define(App\Resources::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->text(50),
//        'resource_code' => $faker->slug(1),
        'resource_type_id' => function() {
            return factory(App\ResourceType::class)->create()->id;
        },
        'rate' => $faker->randomFloat(),
        'waste' => $faker->randomFloat(2, 0, 1),
        'unit' => function() {
            return factory(App\Unit::class)->create()->id;
        }
    ];
});

$factory->define(App\ResourceType::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->text(50),
        'code' => $faker->slug(1),
        'parent_id' => 0
    ];
});

$factory->define('App\Unit', function(Faker\Generator $faker) {
    return ['type' => $faker->text(20)];
});

$factory->define('App\StdActivityResource', function(Faker\Generator $faker) {
    return [
        'template_id' => function() {
            return factory(App\BreakdownTemplate::class)->create()->id;
        },
        'resource_id' => function() {
            return factory(App\Resources::class)->create()->id;
        },
        'equation' => '$v',
        'productivity_id' => 0,
    ];
});

$factory->define(App\ActualResources::class, function(Faker\Generator $faker) {
    $cost = $faker->randomFloat(2, 100, 1000);
    $qty = $faker->randomFloat(2, 100, 1000);

    return [
        'project_id' => function() {
            return factory('App\Project')->create()->id;
        },
        'wbs_level_id' => function() {
            return factory('App\WbsLevel')->create()->id;
        },
        'breakdown_resource_id' => function() {
            return factory('App\BreakdownResource')->create()->id;
        },
        'resource_id' => function() {
            return factory('App\Resources')->create()->id;
        },
        'period_id' => function() {
            return factory('App\Period')->create()->id;
        },
        'cost' => $cost,
        'qty' => $qty,
        'unit_price' => $cost / $qty
    ];
});