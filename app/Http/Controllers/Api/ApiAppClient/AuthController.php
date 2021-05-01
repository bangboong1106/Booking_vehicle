<?php

namespace App\Http\Controllers\Api\ApiAppClient;

use App\Common\HttpCode;
use App\Http\Controllers\Controller;
use App\Model\Entities\AdminUserInfo;
use App\Model\Entities\Customer;
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
            'active' => 1
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

        $customer = Customer::where('user_id', '=', $user->id)->first();

        $user->customer_type = $customer->customer_type;
        $user->customer_id = $customer->id;
        $user->mobile_no = $customer->mobile_no;
        
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
    public function register(Request $request)
    {
        $validation = \Validator::make($request->all(), [
            'username' => 'required',
            'password' => 'required|confirmed',
            'full_name' => 'required',
            'email' => 'required',
        ]);

        if ($validation->fails()) {
            return response()->json([
                'errorCode' => HttpCode::EC_BAD_REQUEST,
                'errorMessage' => $validation->messages()
            ]);
        } else {
            try {
                DB::beginTransaction();

                $user = new AdminUserInfo();

                $user->username = $request->username;
                $user->full_name = $request->full_name;
                $user->email = $request->email;
                $user->password = bcrypt($request->password);
                $user->role = 'customer';
                $user->active = 1;
                $user->ins_id = 1;

                $user->save();

                $customer = new Customer();

                $customer->user_id = $user->id;
                $customer->customer_type = 1;
                $customer->customer_code = 'KH0'.$user->id;
                $customer->full_name = $request->full_name;
                $customer->ins_id = 1;
                $customer->active = 1;

                $customer->save();

                DB::commit();

                return response([
                    'status' => 'success',
                    'msg' => 'Đăng kí thành công'
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
    }
}
