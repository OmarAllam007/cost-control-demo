<?php

namespace App\Providers;

use App\BreakdownTemplate;
use App\StdActivity;
use Illuminate\Support\ServiceProvider;

class IdBreakDownTemplateGenerator extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        BreakdownTemplate::creating(function (BreakdownTemplate $template) {
            $names = explode('»', $template->activity->division->path);

            $code = [];
            foreach ($names as $name) {
                $name = trim($name);
                $code[] = substr(trim($name), 0, 3);
                if (strrchr($name, ' ')) {
                    $position = strrpos($name, ' ');
                    $code[] = substr($name, $position + 1, 1);
                }
                $code[] = '.';
            }
            $activityName = $template->activity->name;
            $code = implode('', $code).substr($activityName,0,3);


            $num = 1;
            $item = BreakdownTemplate::where('code', 'like', $code . '_')->get(['code'])->last();

            if (!is_null($item)) {
                $itemCode = substr($item->resource_code, strrpos($item->resource_code, '.') + 1);
                $itemCode++;
                $code = $code . $itemCode;
                $template->code = $code;
            } else {
                $template->code = $code . $num;
            }

        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
