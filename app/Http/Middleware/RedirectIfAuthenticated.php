<?php

namespace App\Http\Middleware;

use Closure;

class RedirectIfAuthenticated extends Authenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @param  string|null $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        $auth = frontendGuard();
        switch (true) {
            case isBackend():
                $auth = backendGuard();
                break;
            case isApi():
                $auth = apiGuard();
                break;
        }
        if (!$auth->check()) {
            return $this->_toLogin($request);
        }
        return $next($request);
    }

    /**
     * @param $request
     * @return string
     */
    protected function _getBackUrl($request)
    {
        $params = ['return_url' => url()->previous()];
        $url = route(isBackend() ? 'login' : 'frontend.login', $params);
        return $url;
    }
}
