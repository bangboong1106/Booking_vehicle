<?php

namespace App\Http\Controllers\Api\ApiAppManager;

use App\Common\HttpCode;
use App\Http\Controllers\Base\ManagerApiController;
use App\Repositories\Management\UserManagementRepository;
use Auth;
use Illuminate\Http\Request;
use Exception;
use Validator;

class UserApiController extends ManagerApiController
{
    public function __construct(
        UserManagementRepository $repository
    )
    {
        parent::__construct();
        $this->setRepository($repository);
    }

    // Hàm lấy thông tin người dùng đang đăng nhập
    // CreatedBy nlhoang 02/06/2020
    public function getUserInfo()
    {
        try {
            $userId = Auth::User()->id;
            $data = $this->getRepository()->getDataByID($userId);
            return response()->json([
                'errorCode' => HttpCode::EC_OK,
                'errorMessage' => '',
                'data' => $data
            ]);
        } catch (Exception $exception) {
            logError($exception);
            return response()->json(
                [
                    'errorCode' => HttpCode::EC_APPLICATION_ERROR,
                    'errorMessage' => HttpCode::getMessageForCode(HttpCode::EC_APPLICATION_ERROR)
                ]);
        }

    }

}
