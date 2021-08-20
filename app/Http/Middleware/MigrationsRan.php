<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Schema;

class MigrationsRan
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if(!Schema::hasTable('photos')) {
            return redirect(route('home'));
        }
        return $next($request);
    }
}
