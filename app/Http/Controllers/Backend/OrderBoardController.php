<?php

namespace App\Http\Controllers\Backend;

use App\Common\AppConstant;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Yajra\DataTables\DataTables;

class OrderBoardController extends BaseBoardController
{
    protected $_action_list = 'timelineDay,customTimelineWeek,customTwoWeekDate,customTimelineMonth,customDate,hiddenDate refreshButton,deleteButton,exportButton, fullscreenButton';
    protected $_isEditable = true;
    protected $_isRaw = false;

    protected function getModel(): string
    {
        return 'order_board';
    }

    protected function getStatusList()
    {
        return [
            config("constant.CHO_NHAN_HANG"),
            config("constant.DANG_VAN_CHUYEN"),
            config("constant.HOAN_THANH"),
            config("constant.HUY"),
            config("constant.TAI_XE_XAC_NHAN")
        ];
    }

    protected function getOrderListFunction()
    {
        return 'getListForOrderBoard';
    }

    protected function getRepositoryFunction()
    {
        return 'getOrderRepository';
    }

    protected function buildQuery($params)
    {
        return $this->getOrderRepository()->buildQueryForBoard($params);
    }

    public function changeVehicleForTrip(Request $request)
    {
        try {
            DB::beginTransaction();

            $order_id = Request::get('change_trip_id', null);
            $old_vehicle_id = Request::get('change_old_vehicle_id', null);
            $vehicle_id = Request::get('change_vehicle_id', null);
            $etdDate = Request::get('vehicle_start_date', null);
            $etdTime = Request::get('vehicle_start_time', null);
            $etaDate = Request::get('vehicle_end_date', null);
            $etaTime = Request::get('vehicle_end_time', null);
            $primary_driver_id_new = Request::get('change_driver_id', null);

            $order = $this->getOrderRepository()->getItemById($order_id);
            $orderOld = $order->replicate();
            $old_etd_date = $order->ETD_date;
            $old_etd_time = $order->ETD_time;
            $old_eta_date = $order->ETA_date;
            $old_eta_time = $order->ETA_time;
            $order->ETD_date = $etdDate;
            $order->ETD_time = $etdTime;
            $order->ETA_date = $etaDate;
            $order->ETA_time = $etaTime;
            $order->vehicle_id = $vehicle_id;
            $order->primary_driver_id = $primary_driver_id_new;
            $order->route_id = null;
            $order = app('App\Http\Controllers\Backend\OrderController')->_processInputData($order);
            $order->save();

            //Cập nhật ETD, ETA cho location gốc
            $this->getOrderRepository()->updateOrderLocation(
                $order->id,
                $order->location_destination_id,
                config('constant.DESTINATION'),
                $order->ETD_date,
                $order->ETD_time
            );
            $this->getOrderRepository()->updateOrderLocation(
                $order->id,
                $order->location_arrival_id,
                config('constant.ARRIVAL'),
                $order->ETA_date,
                $order->ETA_time
            );

            $primary_driver_id_old = $order->primary_driver_id;

            if ($primary_driver_id_old != $primary_driver_id_new) {
                // Send notification driver old
                if ($primary_driver_id_old != null && $primary_driver_id_old != 0) {
                    $userIdOlds[] = $primary_driver_id_old;
                    $this->getNotificationService()->notifyC20OrPartnerToDriver(2, $userIdOlds, $order);
                }

                // Send notification driver new
                if ($primary_driver_id_new != null && $primary_driver_id_new != 0) {
                    $userIdNews[] = $primary_driver_id_new;
                    $this->getNotificationService()->notifyC20OrPartnerToDriver(1, $userIdNews, $order);
                }
            } else if (
                $old_etd_date != $etdDate || $old_etd_time != $etdTime
                || $old_eta_date != $etaDate || $old_eta_time != $etaTime
            ) {
                $userIds[] = $primary_driver_id_new;
                $this->getNotificationService()->notifyC20OrPartnerToDriver(3, $userIds, $order);
            }

            //Xử lý chuyến
            app('App\Http\Controllers\Backend\RouteController')->_processRouteFromDashboard(2, $order, $vehicle_id, $primary_driver_id_new, $orderOld);

            DB::commit();

            $data = [
                'message' => 'success'
            ];

            $this->setData($data);
            return $this->renderJson();
        } catch (\Exception $e) {
            logError($e . ' - Data :' . json_encode($request));
            DB::rollBack();
        }
    }

    public function removeTripFromVehicle(Request $request)
    {
        try {
            DB::beginTransaction();

            $order_id = Request::get('trip_id', null);
            if ($order_id != null) {
                $orderEntity = $this->getOrderRepository()->getItemById($order_id);
                $orderOld = $orderEntity->replicate();
                $primary_driver_id = $orderEntity->primary_driver_id;
                $orderEntity->status = config("constant.SAN_SANG");
                $orderEntity->vehicle_id = null;
                $orderEntity->primary_driver_id = null;
                $orderEntity->secondary_driver_id = null;
                $orderEntity->route_id = null;
                $orderEntity = app('App\Http\Controllers\Backend\OrderController')->_processInputData($orderEntity);
                $orderEntity->save();

                // Send notification
                if ($primary_driver_id) {
                    $userIds[] = $primary_driver_id;
                    $this->getNotificationService()->notifyC20OrPartnerToDriver(2, $userIds, $orderEntity);
                }

                app('App\Http\Controllers\Backend\RouteController')->_processRouteFromDashboard(4, $orderEntity, null, null, $orderOld);
            }

            DB::commit();

            $data = [
                'message' => 'success'
            ];

            $this->setData($data);
            return $this->renderJson();
        } catch (\Exception $e) {
            logError($e . ' - Data :' . json_encode($request));
            DB::rollBack();
        }
    }

    public function removeTrip(Request $request)
    {
        try {
            DB::beginTransaction();

            $order_id = Request::get('trip_id', null);

            if ($order_id != null) {
                $order = $this->getOrderRepository()->getItemById($order_id);
                $primary_driver_id = $order->primary_driver_id;
                $order_code = $order->order_code;
                $order->delete();
                app('App\Http\Controllers\Backend\OrderController')->_processDeleteTrip($order_id);

                // Send notification
                if ($primary_driver_id) {
                    $userIds[] = $primary_driver_id;
                    $this->getNotificationService()->notifyC20OrPartnerToDriver(2, $userIds, $order);
                }
            }

            DB::commit();

            $data = [
                'message' => 'success'
            ];

            $this->setData($data);
            return $this->renderJson();
        } catch (\Exception $e) {
            logError($e . ' - Data :' . json_encode($request));
            DB::rollBack();
        }
    }


    public function addTrip(Request $request)
    {
        try {
            DB::beginTransaction();

            $vehicle_id = Request::get('vehicle_id', null);
            $order_id = Request::get('order_id', null);
            $primary_driver_id = Request::get('order_driver_id', null);

            $this->updateTrip($vehicle_id, $primary_driver_id, $order_id);

            DB::commit();

            $data = [
                'message' => 'success'
            ];

            $this->setData($data);
            return $this->renderJson();
        } catch (\Exception $e) {
            logError($e . ' - Data :' . json_encode($request));
            DB::rollBack();
        }
    }

    public function updateTrip($vehicle_id, $primary_driver_id, $order_id)
    {
        $order = $this->getOrderRepository()->getItemById($order_id);

        app('App\Http\Controllers\Backend\RouteController')->_processRouteFromDashboard(1, $order, $vehicle_id, $primary_driver_id, null);

        if ($order != null) {
            $order->status = config('constant.TAI_XE_XAC_NHAN');
            $order->vehicle_id = $vehicle_id;
            $order->primary_driver_id = $primary_driver_id;
            $order->driver_id = $primary_driver_id;

            $order = app('App\Http\Controllers\Backend\OrderController')->_processInputData($order);
            $order->save();

            // Send notification
            if ($primary_driver_id) {
                $userIds[] = $primary_driver_id;
                $this->getNotificationService()->notifyC20OrPartnerToDriver(1, $userIds, $order);
            }
        }
    }

    public function massAddTrip()
    {
        try {
            DB::beginTransaction();

            $vehicle_id = Request::get('vehicleID', null);
            $order_ids_str = Request::get('orderIDs', null);
            $primary_driver_id = Request::get('driverID', null);

            $order_ids = explode(',', $order_ids_str);

            foreach ($order_ids as $order_id) {
                $this->updateTrip($vehicle_id, $primary_driver_id, $order_id);
            }

            DB::commit();

            $data = [
                'message' => 'success'
            ];

            $this->setData($data);
            return $this->renderJson();
        } catch (\Exception $e) {
            logError($e);
            DB::rollBack();
        }
    }

    public function resizeDateTrip(Request $request)
    {
        try {
            DB::beginTransaction();

            $id = Request::get('id', null);
            $etd_date_trip = Request::get('trip_start_date', null);
            $etd_time_trip = Request::get('trip_start_time', null);
            $eta_date_trip = Request::get('trip_end_date', null);
            $eta_time_trip = Request::get('trip_end_time', null);
            $primary_driver_id = null;
            $order = null;
            if ($id != null) {
                $order = $this->getOrderRepository()->getItemById($id);
            }
            if ($order != null) {
                $primary_driver_id = $order->primary_driver_id;
                $order->ETD_date = $etd_date_trip;
                $order->ETD_time = $etd_time_trip;
                $order->ETA_date = $eta_date_trip;
                $order->ETA_time = $eta_time_trip;
                $order = app('App\Http\Controllers\Backend\OrderController')->_processInputData($order);
                $order->save();

                //Cập nhật ETD, ETA cho location gốc
                $this->getOrderRepository()->updateOrderLocation(
                    $order->id,
                    $order->location_destination_id,
                    config('constant.DESTINATION'),
                    $order->ETD_date,
                    $order->ETD_time
                );
                $this->getOrderRepository()->updateOrderLocation(
                    $order->id,
                    $order->location_arrival_id,
                    config('constant.ARRIVAL'),
                    $order->ETA_date,
                    $order->ETA_time
                );

                //Xư lý chuyến
                app('App\Http\Controllers\Backend\RouteController')->_processRouteFromDashboard(3, $order, null, null, null);
            }

            // Send notification
            if ($primary_driver_id) {
                $userIds[] = $primary_driver_id;
                $this->getNotificationService()->notifyC20OrPartnerToDriver(3, $userIds, $order);
            }

            DB::commit();

            $data = [
                'message' => 'success'
            ];
            $this->setData($data);
            return $this->renderJson();
        } catch (\Exception $e) {
            logError($e . ' - Data :' . json_encode($request));
            DB::rollBack();
        }
    }
}
