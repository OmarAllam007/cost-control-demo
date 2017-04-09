<?php

namespace App\Http\Middleware;

use App\Support\ChangeLogger;
use Closure;

class ChangeLogMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        app()->instance('change_log', new ChangeLogger());

        return $next($request);
    }
}
