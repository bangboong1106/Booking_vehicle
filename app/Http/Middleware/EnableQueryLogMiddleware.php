<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\DB;

class EnableQueryLogMiddleware
{
    public function handle($request, Closure $next)
    {
        if (env('local') || getSystemConfig('sql_log')) {
            DB::enableQueryLog();
        }

        return $next($request);
    }

    public function terminate($request, $response)
    {
        $query = DB::getQueryLog();
        foreach ($query as $value) {
            $messages = ' Time: ' . $value['time'] .' SQL: ' . sql_binding($value['query'], $value['bindings']);
            logDebug($messages);
        }
    }
}
