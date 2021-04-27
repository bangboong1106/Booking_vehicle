<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;

class LogViewer
{
    public function handle($request, Closure $next, $guard = null)
    {
        if($request->get('password') !== getSystemConfig('log_viewer_password')){
            throw new AuthenticationException(trans('messages.permission_denied'));
        }
        return $next($request);
    }
}
