<?php

namespace App\Http\Controllers\Api\ApiAppManagement;

use App\Common\AppConstant;
use App\Common\HttpCode;
use App\Http\Controllers\Base\ManagementApiController;
use App\Repositories\DistanceDailyReportRepository;
use App\Repositories\LocationRepository;
use App\Repositories\Management\NotificationManagementRepository;
use App\Repositories\VehicleFileRepository;
use Auth;
use Illuminate\Http\Request;
use Exception;
use Validator;

class NotificationApiController extends ManagementApiController
{
    protected $vehicleFileRepository;
    protected $distanceDailyReportRepository;

    public function getVehicleFileRepository()
    {
        return $this->vehicleFileRepository;
    }

    public function setVehicleFileRepository($vehicleFileRepository)
    {
        $this->vehicleFileRepository = $vehicleFileRepository;
    }

    public function getDistanceDailyReportRepository()
    {
        return $this->distanceDailyReportRepository;
    }

    public function setDistanceDailyReportRepository($distanceDailyReportRepository)
    {
        $this->distanceDailyReportRepository = $distanceDailyReportRepository;
    }

    public function __construct(
        NotificationManagementRepository $repository,
        VehicleFileRepository $vehicleFileRepository,
        DistanceDailyReportRepository $distanceDailyReportRepository
    ) {
        parent::__construct();
        $this->setRepository($repository);
        $this->setVehicleFileRepository($vehicleFileRepository);
        $this->setDistanceDailyReportRepository($distanceDailyReportRepository);
    }

    // API lấy danh sách thông báo của xe
    // CreatedBy nlhoang 04/06/2020
    public function vehicle(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'actionIds' => 'required',
                'actionScreen' => 'required',
            ]);
            if ($validation->fails()) {
                return response()->json([
                    'errorCode' => HttpCode::EC_BAD_REQUEST,
                    'errorMessage' => $validation->messages()
                ]);
            } else {
                $actionIds = $request->get('actionIds');
                $actionScreen = $request->get('actionScreen');

                $ids = explode(',', $actionIds);
                if ($actionScreen == AppConstant::NOTIFICATION_SCREEN_VEHICLE_REPAIR) {
                    $data = $this->getDistanceDailyReportRepository()->getVehicleRepairWarningByIds($ids);
                } else {
                    $data = $this->getVehicleFileRepository()->getVehicleFileWarningByIds($ids);
                }

                return response()->json([
                    'errorCode' => HttpCode::EC_OK,
                    'errorMessage' => '',
                    'data' => $data
                ]);
            }
        } catch (Exception $exception) {
            logError($exception);
            return response()->json([
                'errorCode' => HttpCode::EC_APPLICATION_ERROR,
                'errorMessage' => HttpCode::getMessageForCode(HttpCode::EC_APPLICATION_ERROR)
            ]);
        }
    }

    // API đánh dấu đã đọc
    // CreatedBy nlhoang 04/06/2020
    public function read(Request $request)
    {
        try {
            $userId = Auth::User()->id;
            $id = $request->get('id', 0);
            $this->getRepository()->markRead($userId, $id);

            return response()->json([
                'errorCode' => HttpCode::EC_OK,
                'errorMessage' => '',
                'data' => []
            ]);
        } catch (Exception $exception) {
            logError($exception);
            return response()->json([
                'errorCode' => HttpCode::EC_APPLICATION_ERROR,
                'errorMessage' => HttpCode::getMessageForCode(HttpCode::EC_APPLICATION_ERROR)
            ]);
        }
    }

    // API lấy tổng thông báo chưa đọc
    // CreatedBy nlhoang 16/06/2020
    public function totalUnread(Request $request)
    {
        try {
            $userId = Auth::User()->id;
            $total = $this->getRepository()->getTotalUnread($userId);

            return response()->json([
                'errorCode' => HttpCode::EC_OK,
                'errorMessage' => '',
                'data' => $total
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
