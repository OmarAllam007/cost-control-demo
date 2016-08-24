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

elixir(function(mix) {
    mix.sass('app.scss')
        
    
    mix.scripts('tree-select.js')
        .scripts([
                 'breakdown/load-templates.js', 
                 'breakdown/load-resources.js'
                 ], 'public/js/breakdown.js');

    mix.scripts(['jquery.js', 'bootstrap.js'], 'public/js/bootstrap.js');
});
