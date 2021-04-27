<?php

namespace App\Http\Middleware;

use Closure;

/**
 * Class Authenticated
 * @package App\Http\Middleware
 */
class Authenticated
{
    /**
     * @var null
     */
    protected $_guard = null;

    /**
     * Authenticated constructor.
     */
    public function __construct()
    {
        $this->init();
    }

    /**
     *
     */
    public function init()
    {

    }

    /**
     * @return null
     */
    public function getGuard()
    {
        return $this->_guard;
    }

    /**
     * @param null $guard
     */
    public function setGuard($guard)
    {
        $this->_guard = $guard;
    }

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
        if($request->routeIs('backend.login') || ($request->isMethod('POST') && $request->route()->getActionMethod() === 'login')){
            return $next($request);
        }

        // Bỏ validate authenticated route
        if ($request->routeIs('backend.password.request') || $request->routeIs('backend.password.email') || $request->routeIs('backend.password.reset') || $request->routeIs('password.reset') || $request->routeIs('redirect-to-store.redirect') ) {
            return $next($request);
        }

        if (!$this->getGuard()->check()) {
            return $this->_toLogin($request);
        }
        return $next($request);
    }

    /**
     * @param $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    protected function _toLogin($request)
    {
        return redirect($this->_getBackUrl($request));
    }

    /**
     * @param $request
     * @return string
     */
    protected function _getBackUrl($request)
    {
        $params = ['return_url' => $request->fullUrl()];
        $url = route('backend.login', $params);
        return $url;
    }
}
