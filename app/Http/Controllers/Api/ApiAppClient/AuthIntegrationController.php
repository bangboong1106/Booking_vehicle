<?php

namespace App\Http\Controllers\Api\ApiAppClient;

use App\Common\AppConstant;
use App\Common\HttpCode;
use App\Http\Controllers\Base\ApiController;
use App\Model\Entities\AdminUserInfo;
use App\Model\Entities\FcmToken;
use App\Repositories\FcmTokenRepository;
use App\Repositories\Management\UserManagementRepository;
use Exception;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Validator;

class AuthIntegrationController extends ApiController
{
    public $loginAfterSignUp = true;
    protected $fcmTokenRepos;
    protected $userManagementRepository;

    public function getFcmTokenRepos()
    {
        return $this->fcmTokenRepos;
    }

    public function setFcmTokenRepos($fcmTokenRepos)
    {
        $this->fcmTokenRepos = $fcmTokenRepos;
    }

    public function getUserManagementRepository()
    {
        return $this->userManagementRepository;
    }

    public function setUserManagementRepository($userManagementRepository)
    {
        $this->userManagementRepository = $userManagementRepository;
    }

    public function __construct(
        FcmTokenRepository $fcmTokenRepository,
        UserManagementRepository $userManagementRepository
    )
    {
        parent::__construct();
        $this->setFcmTokenRepos($fcmTokenRepository);
        $this->setUserManagementRepository($userManagementRepository);
    }

    public function loginM(Request $request)
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
                    'role' => 'customer', // Chi lay tai khoan customer khi login API
                    'active' => 1
                ];
                $jwt_token = null;

                if (!$jwt_token = JWTAuth::attempt($input)) {
                    return response()->json([
                        'errorCode' => HttpCode::EC_LOGIN_FAILED,
                        'errorMessage' => HttpCode::getMessageForCode(HttpCode::EC_LOGIN_FAILED)
                    ]);
                }

                // log login action from user
                activity()->log(trans('messages.user_logged_in', [
                    'username' => $request['username'],
                    'ip' => request()->ip(),
                ]));

                $userId = Auth::user()->id;
                $user = AdminUserInfo::find($userId);
                if (!empty($user->avatar_id)) {
                    $user->avatarUrl = app('App\Http\Controllers\Api\FileApiController')->getImageUrl($user->avatar_id);
                }
                $user->permissions = $this->getUserManagementRepository()->getPermissionsByUserId($userId);

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
