let mix = require('laravel-mix');

mix.sass('resources/assets/sass/app.scss', 'public/css');
mix.js('resources/assets/js/cost-info-charts.js', 'public/js');
mix.js('resources/assets/js/project-roles/index.js', 'public/js/edit-project-roles.js');