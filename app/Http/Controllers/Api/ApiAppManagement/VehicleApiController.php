<?php

namespace App\Http\Controllers\Api\ApiAppManagement;

use App\Common\HttpCode;
use App\Http\Controllers\Base\ManagementApiController;
use App\Repositories\Management\VehicleManagementRepository;
use Illuminate\Http\Request;
use Exception;
use Validator;
use Illuminate\Support\Facades\Auth;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class VehicleApiController extends ManagementApiController
{

    public function __construct(
        VehicleManagementRepository $customerRepository
    ) {
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


    // Lấy thông tin tài xế mặc định của xe
    //Created by nlhoang 28/01/2021
    public function driver(Request $request)
    {
        try {
            $vehicle_id = $request->get('vehicle_id');
            $data = $this->getRepository()->getDefaultDriver($vehicle_id);
            return response()->json([
                'errorCode' => HttpCode::EC_OK,
                'errorMessage' => '',
                'data' => $data
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
