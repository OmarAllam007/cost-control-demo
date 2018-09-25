const elixir = require('laravel-elixir');
require('laravel-elixir-vue');

elixir(function (mix) {
    // mix.sass('app.scss');
    // mix.sass('print.scss');
    // mix.webpack('project/cost-control.js', 'public/js/cost-control.js');
    // mix.webpack('project/index.js', 'public/js/project.js');
    // mix.webpack('cost-info-charts.js');
    // mix.webpack('rollup/cost-account.js', 'public/js/rollup/cost-account.js');
    // mix.webpack('rollup/semi-activity.js', 'public/js/rollup/semi-activity.js');
    // mix.webpack('rollup/semi-cost-account.js', 'public/js/rollup/semi-cost-account.js');
    //
    // mix.webpack('ActivityLog.js', 'public/js/activity-log.js');
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
    mix.webpack('project-permissions.js');
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
