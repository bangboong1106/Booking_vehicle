<?php

namespace App\Http\Controllers\Api\ApiAppManagement;

use App\Common\HttpCode;
use App\Http\Controllers\Base\ManagementApiController;
use App\Repositories\DriverRepository;
use App\Repositories\Management\DriverManagementRepository;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Auth;
use Validator;

class DriverApiController extends ManagementApiController
{

    public function __construct(
        DriverManagementRepository $driverRepository
    )
    {
        parent::__construct();
        $this->setRepository($driverRepository);
    }


    // Lấy danh sách tài xế thuộc quyền quản lý của user
    //Created by ptly 2020.06.22
    public function getDriversByUser(Request $request)
    {
        try {
            $userId = Auth::user() != null ? Auth::user()->id : 1;
            $drivers = $this->getRepository()->getDataListByUser($request, $userId);
            return response()->json([
                'errorCode' => HttpCode::EC_OK,
                'errorMessage' => '',
                'data' => $drivers
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
