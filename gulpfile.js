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
    mix.sass('print.scss');

    mix.scripts(['jquery.js', 'bootstrap.js'], 'public/js/bootstrap.js');
    mix.scripts('tree-select.js');
    mix.scripts('autocomplete.js');
    mix.scripts([
        'breakdown/load-templates.js',
        'breakdown/load-resources.js',
        'autocomplete.js',
        'tree-select.js'
    ], 'public/js/breakdown.js');

    mix.scripts([
        'vue.js', 'breakdown-resource'
    ], 'public/js/breakdown-resource.js');

    mix.rollup('activity-variables.js');
});
