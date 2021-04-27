<?php

namespace App\Http\Middleware;

use Closure;

class FrontendAuthenticated
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
        $this->setGuard(frontendGuard());
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
        $url = route('frontend.login', $params);
        return $url;
    }
}
