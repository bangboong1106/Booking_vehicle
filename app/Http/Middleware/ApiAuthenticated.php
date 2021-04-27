<?php

namespace App\Http\Middleware;

use App\Common\HttpCode;
use App\Repositories\UserRepository;
use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;

class ApiAuthenticated
{
    protected $_repository = null;

    /**
     * @return null
     */
    public function getRepository()
    {
        return $this->_repository;
    }

    /**
     * @param null $repository
     */
    public function setRepository($repository)
    {
        $this->_repository = $repository;
    }

    public function __construct(UserRepository $userRepository)
    {
        $this->setRepository($userRepository);
    }

    public function handle($request, Closure $next, $guard = null)
    {
        if ($this->auth($request)) {
            return $next($request);
        }
        throw new AuthenticationException(trans('messages.permission_denied'));
    }

    public function auth(Request $request)
    {
        $accessToken = $request->get('access_token');
        if (!$accessToken) {
            return false;
        }
        $user = $this->getRepository()->where('access_token', $accessToken)->first();
        if(empty($user)){
            return false;
        }
        apiGuard()->setUser($user);
        return true;
    }
}
