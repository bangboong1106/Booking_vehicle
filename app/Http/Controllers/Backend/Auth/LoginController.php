<?php

namespace App\Http\Controllers\Backend\Auth;

use App\Http\Controllers\Base\BackendController;
use App\Repositories\AdminUserInfoRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Request;
use Validator;

class LoginController extends BackendController
{
    protected $_area = 'backend';

    protected $_isValidator = false;

    protected $_userLoginHistoryRepository = null;

    /**
     * @return null
     */
    public function getUserLoginHistoryRepository()
    {
        return $this->_userLoginHistoryRepository;
    }

    /**
     * @param null $userLoginHistoryRepository
     */
    public function setUserLoginHistoryRepository($userLoginHistoryRepository)
    {
        $this->_userLoginHistoryRepository = $userLoginHistoryRepository;
    }

    public function __construct(AdminUserInfoRepository $userInfoRepository)
    {
        $this->setRepository($userInfoRepository);
        parent::__construct();
    }

    public function showLoginForm()
    {
        if (backendGuard()->check()) {
            return $this->_redirectToHome();
        }
        $url = Request::get('return_url');
        $this->setViewData(['returnUrl' => $url]);
        $logo = 'McLean-logo-1.png';
        return $this->render('backend.auth.login', compact('logo'));
    }

    public function login()
    {
        $validator = $this->getRepository()->getValidator();
        if (!$validator->validateLogin(Request::all())) {
            return $this->_backWithError($validator->errors());
        }
        $username = Request::get('username');
        $field = filter_var($username, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $user = DB::table('admin_users')
            ->where($field, $username)
            ->whereIn('role',['admin', 'partner'])
            ->select('active')
            ->get();

        $userData = array(
            $field => $username,
            'password' => Request::get('password'),
            'role' => ['admin','partner'],
            'active' => 1,
        );
        
        if ($user != null && empty($user)) {
            if ($user[0]->active == 0)
            {
                $errors = new MessageBag(['active' => [trans('auth.email_password_not_exist')]]);
                return $this->_backWithError($errors);
            }
        }

        if (backendGuard()->attempt($userData)) {
            // log login action
            activity()->log(trans('messages.user_logged_in', [
                'username' => $username,
                'ip' => request()->ip(),
            ]));

            $isFirstTimeLogin = \Auth::user();

            if ($isFirstTimeLogin && !$isFirstTimeLogin->last_login_time && strtotime($isFirstTimeLogin->ins_date) > strtotime("28 October 2020")) {
                Request::replace(['return_url' => $this->_getRouteGetStarted(\Auth::user()->role)]);
            }

            $this->getRepository()->updateLoginTime($isFirstTimeLogin->username);

            return $this->_redirectToHome();

        }

        $errors = new MessageBag(['password' => [trans('auth.email_password_invalid')]]);
        return $this->_backWithError($errors);
    }

    protected function _backWithError($errors)
    {
        return $this->_back()
            ->withErrors($errors)// send back all errors to the login form
            ->withInput(Request::except('password')); // send back the input (not the password) so that we can repopulate the form
    }

    public function logout()
    {
        backendGuard()->logout(); // log the user out of our application
        return $this->_to(route('backend.login'));
        // return $this->_redirectToHome();
    }

    protected function _redirectToHome()
    {
        if (Auth::check() && Auth::user()->role == 'partner' && !Request::get('return_url')) {
            // Request::replace(['return_url' => route('partner-dashboard.index')]);
            Request::replace(['return_url' => route('order-board.index')]);
        }

        $url = Request::get('return_url', route('dashboard.index'));
        $url = !empty($url) ? $url : route('dashboard.index');
        return $this->_to($url);
    }

    protected function _getRouteGetStarted($role)
    {
        $link = [
            'admin' => route('get-started.index'),
            'partner' => route('partner-get-started.index')
        ];

        return array_key_exists($role, $link) ? $link[$role] : $link['admin'];
    }

}
