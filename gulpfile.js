var elixir = require('laravel-elixir');
require('laravel-elixir-vue');

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
    // mix.sass('app.scss');
    // mix.sass('print.scss');
    // mix.webpack('project/index.js', 'public/js/project.js');
    mix.webpack('breakdown-templates/index.js', 'public/js/breakdown-templates.js');
    // mix.webpack('cost-info-charts.js');
    // mix.webpack('project/index.js', 'public/js/project.js');
    // mix.copy('node_modules/c3/c3.min.js', 'public/js/c3.min.js');
    // mix.copy('node_modules/d3/d3.min.js', 'public/js/d3.min.js');
    // mix.webpack('project/cost-control.js', 'public/js/cost-control.js');
    // mix.scripts([
    //     'breakdown/load-templates.js',
    //     'breakdown/load-resources.js',
    //     'breakdown/load-variables.js',
    //     'autocomplete.js',
    //     'tree-select.js'
    // ], 'public/js/breakdown.js');
    // mix.webpack('cost-dashboard/index.js', 'public/js/cost-dashboard.js')
    //     .copy('node_modules/c3/c3.min.js', 'public/css/c3.min.js')
    //     .copy('node_modules/c3/c3.min.css', 'public/css/c3.min.css')
    //
    //
    //     .webpack('project-permissions.js');
    // mix.webpack('breakdown-resource/index.js', 'public/js/breakdown-resource.js');
    // mix.webpack('project/components/Boq.js', 'public/js/public/js/breakdown.js');
    // mix.scripts('tree-select.js');
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




     mix.rollup('activity-variables.js');
     mix.rollup('edit-resource.js');*/
});
