let mix = require('laravel-mix');

mix.sass('resources/assets/sass/app.scss', 'public/css');
mix.sass('resources/assets/sass/print.scss', 'public/css');
mix.js('resources/assets/js/cost-info-charts.js', 'public/js');
mix.js('resources/assets/js/rollup/cost-account.js', 'public/js/rollup/');
mix.js('resources/assets/js/rollup/semi-cost-account.js', 'public/js/rollup/');