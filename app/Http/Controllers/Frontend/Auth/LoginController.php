<?php

namespace App\Http\Controllers\Frontend\Auth;

use App\Http\Controllers\Base\FrontendController;
use App\Model\Entities\Customer;
use App\Repositories\CustomerRepository;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Tymon\JWTAuth\JWTAuth;

class LoginController extends FrontendController
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(CustomerRepository $userRepository)
    {
        $this->setRepository($userRepository);
        parent::__construct();
    }

    public function showLoginForm()
    {
        if (frontendGuard()->check()) {
            return $this->_redirectToHome();
        }
        $this->setTitle('Login');
        return $this->render('frontend.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('username', 'password');
        if (!$token = JWTAuth::attempt($credentials)) {
            return response([
                'status' => 'error',
                'error' => 'invalid.credentials',
                'msg' => 'Invalid Credentials.'
            ], 400);
        }
        return response([
            'status' => 'success'
        ])
            ->header('Authorization', $token);
    }

    public function user(Request $request)
    {
        $user = Customer::find(Auth::user()->id);
        return response([
            'status' => 'success',
            'data' => $user
        ]);
    }

    public function refresh()
    {
        return response([
            'status' => 'success'
        ]);
    }

    public function logout()
    {
        JWTAuth::invalidate();
        return response([
            'status' => 'success',
            'msg' => 'Logged out Successfully.'
        ], 200);
    }

//    public function login()
//    {
//        $validator = $this->getRepository()->getValidator();
//        if (!$validator->validateLogin(Request::all())) {
//            return $this->_backWithError($validator->errors());
//        }
//
//        $userData = array(
//            'username' => Request::get('username'),
//            'password' => Request::get('password')
//        );
//
//        if (frontendGuard()->attempt($userData)) {
//            return $this->_redirectToHome();
//        }
//        $errors = new MessageBag(['password' => [trans('auth.email_password_invalid')]]);
//        return $this->_backWithError($errors);
//    }
//
//    protected function _redirectToHome()
//    {
//        $url = Request::get('return_url', route('home'));
//        return $this->_to($url);
//    }
//
//    protected function _backWithError($errors)
//    {
//        return $this->_back()->withErrors($errors)->withInput(Request::except('password'));
//    }
}
