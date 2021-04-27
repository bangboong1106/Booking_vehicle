<?php

namespace App\Http\Middleware;

use Closure;

class AutoSetLang
{
    public function handle($request, Closure $next)
    {
        setLang();
        return $next($request);
    }

}
