<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Base\BackendController;
use App\Model\Entities\SystemConfig;
use App\Repositories\SystemConfigRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Exception;

/**
 * Class SystemConfigController
 * @package App\Http\Controllers\Backend
 */
class SystemConfigController extends BackendController
{

    public function __construct(SystemConfigRepository $systemConfigRepository)
    {
        parent::__construct();
        $this->setRepository($systemConfigRepository);
        $this->setBackUrlDefault('system-config.index');
        $this->setConfirmRoute('system-config.confirm');
        $this->setMenu('setting');
        $this->setTitle(trans('models.system_config.name'));
    }

    public function index()
    {

        $obj = $this->getRepository()->search()->get();


        $dashboardViewType = $obj
            ->filter(function ($value, $key) {
                return $value->key == "Dashboard.ViewType";
            });

        $dashboardReload = $obj
            ->filter(function ($value, $key) {
                return $value->key == "Dashboard.Reload";
            });
        $dashboardVehiclePageSize = $obj
            ->filter(function ($value, $key) {
                return $value->key == "Dashboard.VehiclePageSize";
            });
        $dashboardNotifyVehicle = $obj
            ->filter(function ($value, $key) {
                return $value->key == "Dashboard.NotifyVehicle";
            });
        $notificationDistanceUnit = $obj
            ->filter(function ($value, $key) {
                return $value->key == "Notification.DistanceUnit";
            });

        $fuelPrice = $obj
            ->filter(function ($value, $key) {
                return $value->key == "Cost.Fuel";
            });

        $driverMobileLimitedTime = $obj
            ->filter(function ($value) {
                return $value->key == "DriverMobile.CompletedLimitTime";
            });
        $driverMobileAllowCancelRoute = $obj
            ->filter(function ($value) {
                return $value->key == "DriverMobile.AllowCancelRoute";
            });
        $driverMobileAllowCancelOrder = $obj
            ->filter(function ($value) {
                return $value->key == "DriverMobile.AllowCancelOrder";
            });
        $driverMobileAllowConfirmOrder = $obj
            ->filter(function ($value) {
                return $value->key == "DriverMobile.AllowConfirmOrder";
            });
        $driverMobileAllowUploadOrder = $obj
            ->filter(function ($value) {
                return $value->key == "DriverMobile.AllowUploadOrder";
            });

        $driverMobileAllowUploadRoute = $obj
            ->filter(function ($value) {
                return $value->key == "DriverMobile.AllowUploadRoute";
            });

        $driverMobileForceUploadBeforeArrival = $obj
            ->filter(function ($value) {
                return $value->key == "DriverMobile.ForceUploadBeforeArrival";
            });
        $this->setViewData([
            'notificationDistanceUnit' => $notificationDistanceUnit->isEmpty() ? 10 : $notificationDistanceUnit->first()->value,
            'dashboardViewType' => $dashboardViewType->isEmpty() ? 'timelineTwoWeek' : $dashboardViewType->first()->value,
            'dashboardReload' => $dashboardReload->isEmpty() ? 5 : $dashboardReload->first()->value,
            'dashboardVehiclePageSize' => $dashboardVehiclePageSize->isEmpty() ? 50 : $dashboardVehiclePageSize->first()->value,
            'dashboardNotifyVehicle' => $dashboardNotifyVehicle->isEmpty() ? 3 : $dashboardNotifyVehicle->first()->value,
            'fuelPrice' => $fuelPrice->isEmpty() ? 0 : $fuelPrice->first()->value,

            'driverMobileLimitedTime' => $driverMobileLimitedTime->isEmpty() ? 0 : $driverMobileLimitedTime->first()->value,
            'driverMobileAllowCancelRoute' => $driverMobileAllowCancelRoute->isEmpty() ? 1 : $driverMobileAllowCancelRoute->first()->value,
            'driverMobileAllowCancelOrder' => $driverMobileAllowCancelOrder->isEmpty() ? 1 : $driverMobileAllowCancelOrder->first()->value,
            'driverMobileAllowConfirmOrder' => $driverMobileAllowConfirmOrder->isEmpty() ? 0 : $driverMobileAllowConfirmOrder->first()->value,
            'driverMobileAllowUploadRoute' => $driverMobileAllowUploadRoute->isEmpty() ? 1 : $driverMobileAllowUploadRoute->first()->value,
            'driverMobileAllowUploadOrder' => $driverMobileAllowUploadOrder->isEmpty() ? 1 : $driverMobileAllowUploadOrder->first()->value,

            'driverMobileForceUploadBeforeArrival' => $driverMobileForceUploadBeforeArrival->isEmpty() ? 0 : $driverMobileForceUploadBeforeArrival->first()->value,

        ]);

        return parent::index();
    }

    public function updateSystemConfig()
    {
        $key = Request::get('Field');
        $value = Request::get('Value');
        try {
            if (SystemConfig::where('key', $key)->exists()) {
                $systemConfig = SystemConfig::firstOrNew(array('key' => $key));
                $systemConfig->value = $value;
                $systemConfig->upd_id = getCurrentUserId();
                $systemConfig->upd_date = date("Y-m-d H:i:s");
                $systemConfig->save();
            } else {
                DB::table('system_config')->insert(
                    array(
                        'key' => $key,
                        'value' => $value,
                        'ins_id' => getCurrentUserId(),
                        'ins_date' => date("Y-m-d H:i:s")
                    )
                );
            }
            $data = [
                'ok' => true
            ];
        } catch (Exception $e) {
            logError($e);
            $data = [
                'ok' => false,
                'message' => $e
            ];
        }
        return json_encode($data);
    }
}
