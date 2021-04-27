<?php

namespace App\Http\Controllers\Api;

use App\Common\AppConstant;
use App\Common\HttpCode;
use App\Http\Controllers\Base\ApiController;
use App\Model\Entities\AdminUserInfo;
use App\Model\Entities\FcmToken;
use App\Repositories\DriverRepository;
use App\Repositories\FcmTokenRepository;
use App\Repositories\NotificationLogDriverRepository;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use JWTAuth;
use Mockery\Exception;
use Tymon\JWTAuth\Exceptions\JWTException;
use Validator;

class DBAppAuthController extends ApiController
{
    public $loginAfterSignUp = true;
    protected $driverRepos;
    protected $notificationLogDriverRepos;
    protected $fcmTokenRepos;

    public function getDriverRepos()
    {
        return $this->driverRepos;
    }

    public function setDriverRepos($driverRepos)
    {
        $this->driverRepos = $driverRepos;
    }

    public function getFcmTokenRepos()
    {
        return $this->fcmTokenRepos;
    }

    public function setFcmTokenRepos($fcmTokenRepos)
    {
        $this->fcmTokenRepos = $fcmTokenRepos;
    }

    public function getNotificationLogDriverRepos()
    {
        return $this->notificationLogDriverRepos;
    }

    public function setNotificationLogDriverRepos($notificationLogDriverRepos)
    {
        $this->notificationLogDriverRepos = $notificationLogDriverRepos;
    }

    public function __construct(DriverRepository $driverRepository, FcmTokenRepository $fcmTokenRepository, NotificationLogDriverRepository $notificationLogDriverRepository)
    {
        parent::__construct();
        $this->setDriverRepos($driverRepository);
        $this->setFcmTokenRepos($fcmTokenRepository);
        $this->setNotificationLogDriverRepos($notificationLogDriverRepository);
    }

    public function login(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'username' => 'required',
                'password' => 'required',
                'fcmToken' => ''
            ]);
            if ($validation->fails()) {
                return response()->json([
                    'errorCode' => HttpCode::EC_BAD_REQUEST,
                    'errorMessage' => $validation->messages()
                ]);
            } else {
                $input = [
                    'username' => $request['username'],
                    'password' => $request['password'],
                    'role' => 'admin' // Chi lay tai khoan Admin khi login API Dashboard
                ];
                $jwt_token = null;

                if (!$jwt_token = JWTAuth::attempt($input)) {
                    return response()->json([
                        'errorCode' => HttpCode::EC_LOGIN_FAILED,
                        'errorMessage' => HttpCode::getMessageForCode(HttpCode::EC_LOGIN_FAILED)
                    ]);
                }

                $user = AdminUserInfo::find(Auth::user()->id);
                if (!empty($user->avatar_id)) {
                    $user->avatarUrl = app('App\Http\Controllers\Api\FileApiController')->getImageUrl($user->avatar_id);
                }

                if (isset($request['fcmToken'])) {
                    if (empty($this->getFcmTokenRepos()->getFcmTokenByUserIdAndToken($user->id, $request['fcmToken']))) {
                        $fcmToken = new FcmToken();
                        $fcmToken->fcm_token = $request['fcmToken'];
                        $fcmToken->user_id = $user->id;
                        $fcmToken->platform_type = AppConstant::PLATFORM_TYPE_MOBILE;
                        $fcmToken->save();
                    }
                }

                return response()->json([
                    'errorCode' => HttpCode::EC_OK,
                    'errorMessage' => '',
                    'data' => [
                        'token' => 'Bearer ' . $jwt_token,
                        'userInfo' => $user
                    ]
                ]);
            }
        } catch (Exception $exception) {
            return response()->json([
                'errorCode' => HttpCode::EC_APPLICATION_ERROR,
                'errorMessage' => HttpCode::getMessageForCode(HttpCode::EC_APPLICATION_ERROR)
            ]);
        }
    }

    public function logout(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'token' => 'required',
                'fcmToken' => ''
            ]);
            if ($validation->fails()) {
                return response()->json([
                    'errorCode' => HttpCode::EC_BAD_REQUEST,
                    'errorMessage' => $validation->messages()
                ]);
            } else {
                $fcm = $this->getFcmTokenRepos()->getFcmFullByToken($request->fcmToken);
                if (!empty($fcm)) {
                    $fcm->delete();
                }

                JWTAuth::invalidate($request->token);
                return response()->json([
                    'errorCode' => HttpCode::EC_OK,
                    'errorMessage' => '',
                    'data' => [
                        'message' => 'ok'
                    ]
                ]);
            }
        } catch (JWTException $exception) {
            return response()->json([
                'errorCode' => HttpCode::EC_APPLICATION_ERROR,
                'errorMessage' => HttpCode::getMessageForCode(HttpCode::EC_APPLICATION_ERROR)
            ]);
        }
    }

    public function refresh()
    {
        return response([
            'status' => 'success'
        ]);
    }

    public function changePassword(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'oldPassword' => 'required',
                'newPassword' => 'required|same:newPasswordConfirm|min:6|max:8',
                'newPasswordConfirm' => 'required|same:newPassword|min:6|max:8'
            ]);
            if ($validation->fails()) {
                return response()->json([
                    'errorCode' => HttpCode::EC_BAD_REQUEST,
                    'errorMessage' => $validation->messages()
                ]);
            } else {
                $currentPassword = Auth::User()->password;

                if (Hash::check($request['oldPassword'], $currentPassword)) {
                    if ($request['oldPassword'] != $request['newPassword']) {
                        $userId = Auth::User()->id;
                        $userObj = AdminUserInfo::find($userId);
                        $userObj->password = Hash::make($request['newPassword']);;
                        $userObj->save();

                        return response()->json([
                            'errorCode' => HttpCode::EC_OK,
                            'errorMessage' => '',
                            'data' => [
                                'message' => 'ok'
                            ]
                        ]);
                    } else {
                        return response()->json([
                            'errorCode' => HttpCode::EC_BAD_REQUEST,
                            'errorMessage' => HttpCode::EM_NEW_PASSWORD_DUPLICATE,
                        ]);
                    }
                } else {
                    return response()->json([
                        'errorCode' => HttpCode::EC_BAD_REQUEST,
                        'errorMessage' => HttpCode::EM_OLD_PASSWORD_NOT_MATCH,
                    ]);
                }
            }
        } catch (Exception $exception) {
            return response()->json([
                'errorCode' => HttpCode::EC_APPLICATION_ERROR,
                'errorMessage' => HttpCode::getMessageForCode(HttpCode::EC_APPLICATION_ERROR)
            ]);
        }
    }
}