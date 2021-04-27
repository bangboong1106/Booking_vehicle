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
use App\Repositories\Driver\RoutesDriverRepository;

use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use JWTAuth;
use Mockery\Exception;
use Tymon\JWTAuth\Exceptions\JWTException;
use Validator;

class AuthIntegrationController extends ApiController
{
    public $loginAfterSignUp = true;
    protected $driverRepos;
    protected $notificationLogDriverRepos;
    protected $fcmTokenRepos;
    protected $routeRepos;

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

    public function getRouteRepos()
    {
        return $this->routeRepos;
    }

    public function setRouteRepos($routeRepos)
    {
        $this->routeRepos = $routeRepos;
    }


    public function __construct(
        DriverRepository $driverRepository,
        FcmTokenRepository $fcmTokenRepository,
        NotificationLogDriverRepository $notificationLogDriverRepository,
        RoutesDriverRepository $routesRepository
    ) {
        parent::__construct();
        $this->setDriverRepos($driverRepository);
        $this->setFcmTokenRepos($fcmTokenRepository);
        $this->setNotificationLogDriverRepos($notificationLogDriverRepository);
        $this->setRouteRepos($routesRepository);
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
                    'role' => 'driver', // Chi lay tai khoan driver khi login API
                    'active' => 1,
                ];
                $jwt_token = null;

                if (!$jwt_token = JWTAuth::attempt($input)) {
                    return response()->json([
                        'errorCode' => HttpCode::EC_LOGIN_FAILED,
                        'errorMessage' => HttpCode::getMessageForCode(HttpCode::EC_LOGIN_FAILED)
                    ]);
                }

                $user = AdminUserInfo::find(Auth::user()->id);
                $driver = $this->getDriverRepos()->getFullInfoDriverWithUserId($user->id);
                $user->ready_status = $driver->ready_status;
                if (!empty($driver->avatar_id)) {
                    $user->avatarUrl = app('App\Http\Controllers\Api\FileApiController')->getImageUrl($driver->avatar_id);
                }

                if (isset($request['fcmToken']) && isset($driver)) {
                    if (empty($this->getFcmTokenRepos()->getFcmTokenByUserIdAndToken($user->id, $request['fcmToken']))) {
                        $fcmToken = new FcmToken();
                        $fcmToken->fcm_token = $request['fcmToken'];
                        $fcmToken->user_id = $user->id;
                        $fcmToken->driver_id = $driver->id;
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

    // Lay count cac tin o cac tab mobile
    public function loadCountTabs(Request $request)
    {
        try {
            $countRouteIncomplete = 0;
            $countRouteCompleted = 0;
            $countRouteCancel = 0;
            $countNotificationUnread = 0;

            // TODO: Remove in new version
            $countConfirmOrder = 0;
            $countNewOrder = 0;
            $countMovingOrder = 0;
            $countCompletedOrder = 0;
            $countCancelOrder = 0;

            $userId = Auth::User()->id;
            $driverObj = $this->getDriverRepos()->getFullInfoDriverWithUserId($userId);
            if (isset($driverObj)) {
                // TODO: Remove in new version
                $countObj = $this->getDriverRepos()->countOrderByDriverIdAndStatus($driverObj->id);
                if (isset($countObj)) {
                    foreach ($countObj as $item) {
                        switch ($item->status) {
                            case config('constant.TAI_XE_XAC_NHAN'):
                                $countConfirmOrder = $item->count;
                                break;
                            case config('constant.CHO_NHAN_HANG'):
                                $countNewOrder = $item->count;
                                break;
                            case config('constant.DANG_VAN_CHUYEN'):
                                $countMovingOrder = $item->count;
                                break;
                            case config('constant.HOAN_THANH'):
                                $countCompletedOrder = $item->count;
                                break;
                            case config('constant.HUY'):
                                $countCancelOrder = $item->count;
                                break;
                        }
                    }
                }

                $countRoute = $this->getRouteRepos()->countRouteByDriverId($driverObj->id);
                if (isset($countRoute)) {
                    foreach ($countRoute as $item) {
                        switch ($item->status) {
                            case config('constant.status_incomplete'):
                                $countRouteIncomplete = $item->count;
                                break;
                            case config('constant.status_complete'):
                                $countRouteCompleted = $item->count;
                                break;
                            case config('constant.status_cancel'):
                                $countRouteCancel = $item->count;
                                break;
                        }
                    }
                }
                $countNotificationUnread = $this->getNotificationLogDriverRepos()->countUnreadLogByDriverId($driverObj->id);
            }
            return response()->json([
                'errorCode' => HttpCode::EC_OK,
                'errorMessage' => '',
                'data' => [
                    'countNotificationUnread' => $countNotificationUnread,
                    'countRouteIncomplete' => $countRouteIncomplete,
                    'countRouteCompleted' => $countRouteCompleted,
                    'countRouteCancel' => $countRouteCancel,
                    // TODO: Remove in new version
                    'countNewOrder' => $countNewOrder,
                    'countConfirmOrder' => $countConfirmOrder,
                    'countMovingOrder' => $countMovingOrder,
                    'countCompletedOrder' => $countCompletedOrder,
                    'countCancelOrder' => $countCancelOrder
                ]
            ]);
        } catch (Exception $exception) {
            return response()->json([
                'errorCode' => HttpCode::EC_APPLICATION_ERROR,
                'errorMessage' => HttpCode::getMessageForCode(HttpCode::EC_APPLICATION_ERROR)
            ]);
        }
    }
}
