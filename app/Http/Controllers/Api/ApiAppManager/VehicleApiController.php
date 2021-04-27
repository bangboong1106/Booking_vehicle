<?php

namespace App\Http\Controllers\Api\ApiAppManager;

use App\Common\HttpCode;
use App\Http\Controllers\Base\ManagerApiController;
use App\Repositories\Management\VehicleManagementRepository;
use Illuminate\Http\Request;
use Exception;
use Validator;
use Illuminate\Support\Facades\Auth;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class VehicleApiController extends ManagerApiController
{

    public function __construct(
        VehicleManagementRepository $customerRepository
    )
    {
        parent::__construct();
        $this->setRepository($customerRepository);
    }

    public function map()
    {
        try {
            $userId = Auth::user() != null ? Auth::user()->id : 1;
            $vehicles = $this->getRepository()->getVehiclesByUser($userId);
            return response()->json([
                'errorCode' => HttpCode::EC_OK,
                'errorMessage' => '',
                'data' => $vehicles
            ]);

        } catch (Exception $exception) {
            logError($exception);
            return response()->json([
                'errorCode' => HttpCode::EC_APPLICATION_ERROR,
                'errorMessage' => HttpCode::getMessageForCode(HttpCode::EC_APPLICATION_ERROR)
            ]);
        }

    }

    // Lấy danh sách xe thuộc quyền quản lý của user
    //Created by ptly 2020.06.22
    public function getVehiclesByUser(Request $request)
    {
        try {
            $userId = Auth::user() != null ? Auth::user()->id : 1;
            $vehicles = $this->getRepository()->getDataListByUser($request, $userId);
            return response()->json([
                'errorCode' => HttpCode::EC_OK,
                'errorMessage' => '',
                'data' => $vehicles
            ]);

        } catch (Exception $exception) {
            logError($exception);
            return response()->json([
                'errorCode' => HttpCode::EC_APPLICATION_ERROR,
                'errorMessage' => HttpCode::getMessageForCode(HttpCode::EC_APPLICATION_ERROR)
            ]);
        }

    }

}
