var elixir = require('laravel-elixir');
require('laravel-elixir-vue');

elixir(function (mix) {
    mix.sass('app.scss');
    mix.webpack('rollup/create.js', 'public/js/rollup/create.js');
    mix.webpack('rollup/edit.js', 'public/js/rollup/edit.js');
});
