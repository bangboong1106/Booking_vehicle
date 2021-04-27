<?php

namespace App\Http\Controllers\Api;

use App\Common\HttpCode;
use App\Http\Controllers\Controller;
use App\Model\Entities\AdminUserInfo;
use http\Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use JWTAuth;
use Validator;

class AuthController extends Controller
{

    public function login(Request $request)
    {
        $credentials = [
            'username' => $request['username'],
            'password' => $request['password'],
            'role' => 'customer', // Lay tai khoan customer khi login
            'active' => 1,
        ];
        $jwt_token = null;

        if (!$token = JWTAuth::attempt($credentials)) {
            return response([
                'status' => 'error',
                'error' => 'invalid.credentials',
                'msg' => 'Invalid Credentials.'
            ], 400);
        }

        // log login action from user
        activity()->log(trans('messages.user_logged_in', [
            'username' => $request['username'],
            'ip' => request()->ip(),
        ]));

        return response([
            'status' => 'success'
        ])
            ->header('Authorization', $token);
    }

    public function user(Request $request)
    {
        $user = AdminUserInfo::find(Auth::user()->id);
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

    public function changePassword(Request $request)
    {
        try {
            DB::beginTransaction();
            $user = AdminUserInfo::find(Auth::user()->id);
            $currentPassword = $request->post('current_password');
            $password = $request->post('password');
            if (!Hash::check($currentPassword, $user->password)) {
                return response([
                    'status' => 'error',
                    'msg' => 'Mật khẩu hiện tại không đúng'
                ], 200);
            } else {
                if ($currentPassword == $password) {
                    return response([
                        'status' => 'error',
                        'msg' => 'Mật khẩu mới trùng mật khẩu hiện tại'
                    ], 200);
                } else {
                    $user->password = $password;
                    $user->save();
                }
            }
            DB::commit();
            return response([
                'status' => 'success',
                'msg' => 'Thay đổi mật khẩu thành công'
            ], 200);
        } catch (\Exception $e) {
            logError($e);
            return response([
                'status' => 'error',
                'msg' => 'Lỗi hệ thống'
            ], 200);
            DB::rollBack();
        }
    }

    public function login3P(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'username' => 'required',
                'password' => 'required'
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
                    'role' => 'customer', // Chi lay tai khoan customer khi login
                    'active' => 1,
                ];
                $jwt_token = null;

                if (!$jwt_token = JWTAuth::attempt($input)) {
                    return response()->json([
                        'errorCode' => HttpCode::EC_LOGIN_FAILED,
                        'errorMessage' => HttpCode::getMessageForCode(HttpCode::EC_LOGIN_FAILED)
                    ]);
                }

                return response()->json([
                    'errorCode' => HttpCode::EC_OK,
                    'errorMessage' => '',
                    'data' => [
                        'token' => 'Bearer ' . $jwt_token
                    ]
                ]);
            }
        } catch (\Exception $exception) {
            return response()->json([
                'errorCode' => HttpCode::EC_APPLICATION_ERROR,
                'errorMessage' => HttpCode::getMessageForCode(HttpCode::EC_APPLICATION_ERROR)
            ]);
        }
    }
}