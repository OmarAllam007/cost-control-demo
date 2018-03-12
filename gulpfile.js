var elixir = require('laravel-elixir');
require('laravel-elixir-vue');

elixir(function (mix) {
    // mix.sass('app.scss');
    // mix.webpack('rollup/cost-account.js', 'public/js/rollup/cost-account.js');
    // mix.webpack('rollup/semi-cost-account.js', 'public/js/rollup/semi-cost-account.js');
    // mix.webpack('rollup/semi-activity.js', 'public/js/rollup/semi-activity.js');
    // mix.webpack('project/cost-control.js', 'public/js/cost-control.js');
    mix.webpack('ActivityLog.js', 'public/js/activity-log.js');
});
