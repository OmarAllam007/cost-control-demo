var elixir = require('laravel-elixir');

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Sass
 | file for our application, as well as publishing vendor resources.
 |
 */

elixir(function (mix) {
    mix.sass('app.scss');
    mix.webpack('project/cost-control.js', 'public/js/cost-control.js')
        .webpack('project/index.js', 'public/js/project.js')
        .webpack('project-permissions.js');
    mix.webpack('breakdown-resource/index.js', 'public/js/breakdown-resource.js');
    mix.webpack('project/components/Boq.js', 'public/js/public/js/breakdown.js');
    mix.scripts('tree-select.js');
    // .sass('print.scss')
    // .webpack('resource-codes.js');
//comment again
//     mix.scripts([
//         'breakdown/load-templates.js',
//         'breakdown/load-resources.js',
//         'breakdown/load-variables.js',
//         'autocomplete.js',
//         'tree-select.js'
//     ], 'public/js/breakdown.js');

    // mix.scripts([
    //     'project/components/Breakdown.js',
    // ], 'public/js/breakdown.js');
    // mix.webpack('project-permissions.js')
    //     .webpack('project/index.js', 'public/js/project.js');

    // .sass('print.scss')
    // .webpack('resource-codes.js');


    /*
     mix.scripts(['jquery.js', 'bootstrap.js'], 'public/js/bootstrap.js');

     mix.scripts('autocomplete.js');
     mix.scripts([
     'breakdown/load-templates.js',
     'breakdown/load-resources.js',
     'breakdown/load-variables.js',
     'autocomplete.js',
     'tree-select.js'
     ], 'public/js/breakdown.js');



     mix.rollup('activity-variables.js');
     mix.rollup('edit-resource.js');*/
});
