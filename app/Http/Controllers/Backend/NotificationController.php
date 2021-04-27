<?php

namespace App\Http\Controllers\Backend;

use App\Common\AppConstant;
use App\Http\Controllers\Base\BackendController;
use App\Repositories\DistanceDailyReportRepository;
use App\Repositories\NotificationLogRepository;
use App\Repositories\VehicleFileRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;

/**
 * Class NotificationController
 * @package App\Http\Controllers\Backend
 */
class NotificationController extends BackendController
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

    public function __construct(NotificationLogRepository $notificationRepository, VehicleFileRepository $vehicleFileRepository,
                                DistanceDailyReportRepository $distanceDailyReportRepository)
    {
        parent::__construct();
        $this->setRepository($notificationRepository);
        $this->setVehicleFileRepository($vehicleFileRepository);
        $this->setDistanceDailyReportRepository($distanceDailyReportRepository);
        $this->setBackUrlDefault('notification.index');
        $this->setTitle(trans('models.notification.name'));

    }

    public function notifyNearestLocation()
    {
        $procedureName = 'proc_get_orders_nearest_vehicles';
        $sql = 'call ' . $procedureName . '()';
        $query = DB::select($sql);

        $results = json_encode(["data" => $query], JSON_NUMERIC_CHECK);
        return $results;
    }

    public function loadNotifyPage()
    {
        try {
            $user = $this->getCurrentUser();
            $page = Request::get('page');
            $datas = $this->getRepository()->getNotificationLogs($user->id, $page);

        } catch (\Exception $e) {
            $datas = [];
            logError($e);
        }

        $this->setViewData([
            'datas' => $datas
        ]);
        $html = [
            'content' => $this->render('backend.notification._list')->render(),
        ];
        $this->setData($html);
        return $this->renderJson();
    }

    public function vehicleNotifyDetail()
    {
        $vehicleWarning = [];
        $vehicleRepairWarning = [];
        try {
            $actionIds = Request::get('actionIds');
            $actionScreen = Request::get('actionScreen');

            $ids = explode(',', $actionIds);
            if ($actionScreen == AppConstant::NOTIFICATION_SCREEN_VEHICLE_REPAIR) {
                $vehicleRepairWarning = $this->getDistanceDailyReportRepository()->getVehicleRepairWarningByIds($ids);
            } else {
                $vehicleWarning = $this->getVehicleFileRepository()->getVehicleFileWarningByIds($ids);
            }

        } catch (\Exception $e) {
            logError($e);
        }

        $this->setViewData([
            'datas' => $vehicleWarning,
            'dataRepair' => $vehicleRepairWarning
        ]);
        $html = [
            'content' => $this->render('backend.notification._vehicle_notify_detail')->render(),
        ];
        $this->setData($html);
        return $this->renderJson();
    }

}
