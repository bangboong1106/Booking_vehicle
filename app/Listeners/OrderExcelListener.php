<?php

namespace App\Listeners;

use App\Common\AppConstant;
use App\Helpers\Facades\BatchFacade as Batch;
use App\Model\Entities\RouteCost;
use App\Model\Entities\Routes;
use App\Repositories\LocationRepository;
use App\Repositories\OrderHistoryRepository;
use App\Repositories\OrderPaymentRepository;
use App\Repositories\OrderRepository;
use App\Repositories\QuotaCostRepository;
use App\Repositories\RoutesRepository;
use App\Repositories\TPActionSyncRepository;
use App\Repositories\VehicleRepository;
use App\Services\RouteService;
use Illuminate\Support\Facades\Auth;

class OrderExcelListener extends CustomBaseListener
{
    protected $_orderRepos;
    protected $_tpActionSyncRepository;
    protected $_orderPaymentRepos;
    protected $_orderHistoryRepository;
    protected $_routesRepos;
    protected $_locationRepos;
    protected $_vehicleRepos;
    protected $_quotaCostRepos;
    protected $_routeService;

    /**
     * @return mixed
     */
    public function getTpActionSyncRepository()
    {
        return $this->_tpActionSyncRepository;
    }

    /**
     * @param mixed $tpActionSyncRepository
     */
    public function setTpActionSyncRepository($tpActionSyncRepository): void
    {
        $this->_tpActionSyncRepository = $tpActionSyncRepository;
    }

    /**
     * @return mixed
     */
    public function getOrderPaymentRepos()
    {
        return $this->_orderPaymentRepos;
    }

    /**
     * @param mixed $orderPaymentRepos
     */
    public function setOrderPaymentRepos($orderPaymentRepos): void
    {
        $this->_orderPaymentRepos = $orderPaymentRepos;
    }

    /**
     * @return mixed
     */
    public function getOrderHistoryRepository()
    {
        return $this->_orderHistoryRepository;
    }

    /**
     * @param mixed $orderHistoryRepository
     */
    public function setOrderHistoryRepository($orderHistoryRepository): void
    {
        $this->_orderHistoryRepository = $orderHistoryRepository;
    }

    /**
     * @return mixed
     */
    public function getOrderRepos()
    {
        return $this->_orderRepos;
    }

    /**
     * @param mixed $orderRepos
     */
    public function setOrderRepos($orderRepos): void
    {
        $this->_orderRepos = $orderRepos;
    }

    /**
     * @return mixed
     */
    public function getRoutesRepos()
    {
        return $this->_routesRepos;
    }

    /**
     * @param mixed $routesRepos
     */
    public function setRoutesRepos($routesRepos): void
    {
        $this->_routesRepos = $routesRepos;
    }

    /**
     * @return mixed
     */
    public function getLocationRepos()
    {
        return $this->_locationRepos;
    }

    /**
     * @param mixed $locationRepos
     */
    public function setLocationRepos($locationRepos): void
    {
        $this->_locationRepos = $locationRepos;
    }

    /**
     * @return mixed
     */
    public function getVehicleRepos()
    {
        return $this->_vehicleRepos;
    }

    /**
     * @param mixed $vehicleRepos
     */
    public function setVehicleRepos($vehicleRepos): void
    {
        $this->_vehicleRepos = $vehicleRepos;
    }

    /**
     * @return mixed
     */
    public function getQuotaCostRepos()
    {
        return $this->_quotaCostRepos;
    }

    /**
     * @param mixed $quotaCostRepos
     */
    public function setQuotaCostRepos($quotaCostRepos): void
    {
        $this->_quotaCostRepos = $quotaCostRepos;
    }

    /**
     * @return mixed
     */
    public function getRouteService()
    {
        return $this->_routeService;
    }

    /**
     * @param mixed $routeService
     */
    public function setRouteService($routeService): void
    {
        $this->_routeService = $routeService;
    }

    /**
     * Create the event listener.
     *
     * @param OrderRepository $orderRepository
     * @param TPActionSyncRepository $tpActionSyncRepository
     * @param OrderPaymentRepository $orderPaymentRepository
     * @param OrderHistoryRepository $orderHistoryRepository
     * @param RoutesRepository $routesRepository
     * @param LocationRepository $locationRepository
     * @param VehicleRepository $vehicleRepository
     * @param QuotaCostRepository $quotaCostRepository
     * @param RouteService $routeService
     */
    public function __construct(OrderRepository $orderRepository,
                                TPActionSyncRepository $tpActionSyncRepository,
                                OrderPaymentRepository $orderPaymentRepository,
                                OrderHistoryRepository $orderHistoryRepository,
                                RoutesRepository $routesRepository,
                                LocationRepository $locationRepository,
                                VehicleRepository $vehicleRepository,
                                QuotaCostRepository $quotaCostRepository,
                                RouteService $routeService
    )
    {
        $this->setOrderRepos($orderRepository);
        $this->setTPActionSyncRepository($tpActionSyncRepository);
        $this->setOrderPaymentRepos($orderPaymentRepository);
        $this->setOrderHistoryRepository($orderHistoryRepository);
        $this->setRoutesRepos($routesRepository);
        $this->setLocationRepos($locationRepository);
        $this->setVehicleRepos($vehicleRepository);
        $this->setQuotaCostRepos($quotaCostRepository);
        $this->setRouteService($routeService);
    }

    /**
     * Handle the event.
     *
     * @param $eventData
     * @return void
     */
    public function handleBusiness($eventData)
    {
        $update = $eventData['update'];
        $orderList = $eventData['orderList'];
        $orderOldList = $eventData['orderOldList'];
        $routeIdUpdates = $eventData['routeIdUpdates'];
        $userId = $eventData['userId'];

        //Xử lý thông tin đơn
        $this->handleOrder($update, $orderList, $orderOldList, $userId);

        //Xử lý thông tin chuyến
        $this->handleRoute($routeIdUpdates, $userId);

    }

    public function handleOrder($update, $orderList, $orderOldList, $userId)
    {
        foreach ($orderList as $order) {

            $orderNew = $this->getOrderRepos()->findFirstOrNew($order);

            $orderOld = null;
            if ($update)
                $orderOld = $this->getOrderRepos()->findFirstOrNew($orderOldList[$orderNew->id]);

            //Trigger tạo bản ghi đồng bộ đối tác
            if ($update && isset($orderOldList[$orderNew->id])) {
                $this->getTPActionSyncRepository()->triggerActionSync($orderOld, $orderNew);
            } else {
                $this->getTPActionSyncRepository()->triggerActionSync(null, $orderNew);
            }

            $vehicleId = isset($orderNew->vehicle_id) ? $orderNew->vehicle_id : null;
            $primaryDriverId = isset($orderNew->primary_driver_id) ? $orderNew->primary_driver_id : null;
            $secondaryDriverId = isset($orderNew->secondary_driver_id) ? $orderNew->secondary_driver_id : null;

            //Luu order_history va notify
            if (!$update) {
                $this->getOrderHistoryRepository()->processCreateOrderHistory($orderNew, $vehicleId, $primaryDriverId, $secondaryDriverId);

                if (!isset($entity->is_merge_item) || $entity->is_merge_item != config('constant.yes'))
                    // send notification to driver
                    if (!empty($vehicleId) && !empty($primaryDriverId)) {
                        unset($assignUserIds);
                        $assignUserIds[] = $primaryDriverId;
                        $title = AppConstant::generateTitlePN();
                        $message = AppConstant::generateMessagePN($orderNew->order_code);
                        $dataPN = app('App\Http\Controllers\Backend\OrderController')->_getDataPushFromOrder($orderNew);
                        app('App\Http\Controllers\Api\AlertLogApiController')->pushNotificationToDriver($assignUserIds, $title, $message, null, $dataPN);
                    }
            } else {
                if ($orderNew && $orderOld && $orderNew->status != $orderOld->status) {
                    $this->getOrderHistoryRepository()->processCreateOrderHistory($orderNew, $vehicleId, $primaryDriverId, $secondaryDriverId);
                } else {
                    $this->getOrderHistoryRepository()->processUpdateOrderHistory($orderNew, $vehicleId, $primaryDriverId, $secondaryDriverId);
                }

                $primaryDriverIdOld = $orderOld && isset($orderOld->primary_driver_id) ? $orderOld->primary_driver_id : 0;
                if (!isset($orderNew->is_merge_item) || $orderNew->is_merge_item != config('constant.yes')) {
                    $primaryDriverIdNew = $orderNew && isset($orderNew->primary_driver_id) ? $orderNew->primary_driver_id : 0;
                    if ($primaryDriverIdOld != $primaryDriverIdNew) {
                        // Send notification driver old
                        if ($primaryDriverIdOld && $primaryDriverIdOld != 0) {
                            $cancelUserIds[] = $primaryDriverIdOld;
                            $title = AppConstant::generateTitlePN();
                            $message = AppConstant::generateMessageDeletePN($orderNew->order_code);
                            $dataPN = "";
                            app('App\Http\Controllers\Api\AlertLogApiController')->pushNotificationToDriver($cancelUserIds, $title, $message, null, $dataPN);
                        }

                        // Send notification driver new
                        if ($primaryDriverIdNew != null && $primaryDriverIdNew != 0) {
                            $assignUserIds[] = $primaryDriverIdNew;
                            $title = AppConstant::generateTitlePN();
                            $message = AppConstant::generateMessagePN($orderNew->order_code);
                            $dataPN = app('App\Http\Controllers\Backend\OrderController')->_getDataPushFromOrder($orderNew);
                            app('App\Http\Controllers\Api\AlertLogApiController')->pushNotificationToDriver($assignUserIds, $title, $message, null, $dataPN);
                        }
                    } else {
                        if ($orderOld && $primaryDriverIdNew && $primaryDriverIdNew != 0) {
                            $assignUserIds[] = $primaryDriverIdNew;
                            $title = AppConstant::generateTitlePN();

                            // Notify driver khi đơn hàng bị huỷ , về sẵn sàng , khởi tạo
                            if (in_array($orderNew->status, [config("constant.HUY"), config("constant.SAN_SANG"), config("constant.KHOI_TAO")])) {
                                $message = AppConstant::generateMessageDeletePN($orderNew->order_code);
                            } else {
                                $message = AppConstant::generateMessageWhenEditOrderPN($orderOld, $orderNew);
                            }
                            if ($message != "") {
                                $dataPN = app('App\Http\Controllers\Backend\OrderController')->_getDataPushFromOrder($orderNew);
                                app('App\Http\Controllers\Api\AlertLogApiController')->pushNotificationToDriver($assignUserIds, $title, $message, null, $dataPN);
                            }
                        }
                    }
                }
            }

            //Xử lý DHKH
            if ($orderNew) {
                if ($update)
                    $orderNew->upd_id = $userId;
                else
                    $orderNew->ins_id = $userId;
            }
            app('App\Http\Controllers\Backend\OrderCustomerController')->_processOrderCustomerFromOrder($orderNew, $orderOld, $userId);
        }
    }

    public function handleRoute($routeIdUpdates, $userId)
    {
        $routeUpdates = [];
        $routeCostDeletes = [];

        if ($routeIdUpdates) {
            $routeOrders = [];
            $orderList = $this->getOrderRepos()->getOrdersByRouteIds($routeIdUpdates);
            foreach ($orderList as $order) {
                $routeOrders[$order->route_id][] = $order;
            }

            $locationList = $this->getLocationRepos()->search()->get()->pluck('title', 'id')->toArray();
            $vehicles = $this->getVehicleRepos()->search()->get();
            $vehicleList = [];
            if ($vehicles)
                foreach ($vehicles as $vehicle) {
                    $vehicleList[$vehicle->id] = $vehicle;
                }

            $routeEntities = $this->getRoutesRepos()->getItemsByIds($routeIdUpdates);
            foreach ($routeEntities as $routeEntity) {
                if (isset($routeOrders[$routeEntity->id])) {
                    $route = [];
                    $route['id'] = $routeEntity->id;

                    $ETD_date = null;
                    $ETD_time = null;
                    $ETA_date = null;
                    $ETA_time = null;
                    $ETD_date_reality = null;
                    $ETD_time_reality = null;
                    $ETA_date_reality = null;
                    $ETA_time_reality = null;
                    $totalWeight = 0;
                    $totalVolume = 0;
                    $location_destination_id = null;
                    $location_arrival_id = null;
                    $location_destination_title = '';
                    $location_arrival_title = '';

                    $status = config('constant.status_incomplete');

                    $countCancel = 0;
                    $countComplete = 0;
                    foreach ($routeOrders[$routeEntity->id] as $order) {
                        if ($order->status == config('constant.HUY'))
                            $countCancel++;
                        if ($order->status == config('constant.HOAN_THANH'))
                            $countComplete++;

                        if ($order->ETD_date != null) {
                            $dateTime1 = $order->ETD_date . ' ' . ($order->ETD_time ? $order->ETD_time : '');
                            $dateTime2 = $ETD_date . ' ' . ($ETD_time ? $ETD_time : '');
                            if ($ETD_date == null || AppConstant::isDate2GreatDate1($dateTime1, $dateTime2)) {
                                $ETD_date = $order->ETD_date;
                                $ETD_time = $order->ETD_time;
                                $location_destination_id = $order->location_destination_id;
                            }
                        }
                        if ($order->ETA_date != null) {
                            $dateTime2 = $order->ETA_date . ' ' . ($order->ETA_time ? $order->ETA_time : '');
                            $dateTime1 = $ETA_date . ' ' . ($ETA_time ? $ETA_time : '');
                            if ($ETA_date == null || AppConstant::isDate2GreatDate1($dateTime1, $dateTime2)) {
                                $ETA_date = $order->ETA_date;
                                $ETA_time = $order->ETA_time;
                                $location_arrival_id = $order->location_arrival_id;
                            }
                        }

                        if ($order->status == config('constant.HOAN_THANH') && $order->ETA_date_reality != null) {
                            $dateTime2 = $order->ETA_date_reality . ' ' . ($order->ETA_time_reality ? $order->ETA_time_reality : '');
                            $dateTime1 = $ETA_date_reality . ' ' . ($ETA_time_reality ? $ETA_time_reality : '');
                            if ($ETA_date_reality == null || AppConstant::isDate2GreatDate1($dateTime1, $dateTime2)) {
                                $ETA_date_reality = $order->ETA_date_reality;
                                $ETA_time_reality = $order->ETA_time_reality;
                            }
                        }
                        if ($order->ETD_date_reality != null) {
                            $dateTime1 = $order->ETD_date_reality . ' ' . ($order->ETD_time_reality ? $order->ETD_time_reality : '');
                            $dateTime2 = $ETD_date_reality . ' ' . ($ETD_time_reality ? $ETD_time_reality : '');
                            if ($ETD_date_reality == null || AppConstant::isDate2GreatDate1($dateTime1, $dateTime2)) {
                                $ETD_date_reality = $order->ETD_date_reality;
                                $ETD_time_reality = $order->ETD_time_reality;
                            }
                        }

                        $totalWeight += isset($order->weight) && is_numeric($order->weight) ? $order->weight : 0;
                        $totalVolume += isset($order->volume) && is_numeric($order->volume) ? $order->volume : 0;
                    }

                    $location_destination_title = isset($locationList[$location_destination_id]) ? $locationList[$location_destination_id] : '';
                    $location_arrival_title = isset($locationList[$location_arrival_id]) ? $locationList[$location_arrival_id] : '';
                    $routeName = $location_destination_title . "-" . $location_arrival_title;

                    //Cập nhật route cost nếu thay đổi điểm đầu , điểm cuối của chuyến nếu chuyến chưa phê duyệt
                    // hoặc chưa có chi phí khi tạo mới

                    if ($routeEntity->quota_id == null || $routeEntity->quota_id == 0
                        || ($routeEntity->is_approved != config('constant.DA_PHE_DUYET')
                            && ($routeEntity->location_destination_id != $location_destination_id
                                || $routeEntity->location_arrival_id != $location_arrival_id))) {

                        // Tạo chi phí cho chuyến
                        $quota = app('App\Http\Controllers\Backend\QuotaController')->findFirsOrNewQuotaByLocation(
                            $location_destination_id,
                            $location_destination_title,
                            $location_arrival_id,
                            $location_arrival_title,
                            isset($vehicleList[$routeEntity->vehicle_id]) ? $vehicleList[$routeEntity->vehicle_id]->group_id : null
                        );

                        if ($quota && $quota->id != $routeEntity->quota_id) {
                            $route['quota_id'] = $quota->id;

                            $quotaCosts = $this->getQuotaCostRepos()->getCosts($quota->id);
                            $quotaCostList = [];
                            if ($quotaCosts)
                                foreach ($quotaCosts as $cost) {
                                    $quotaCostList[] = [
                                        'receipt_payment_id' => $cost->receipt_payment_id,
                                        'receipt_payment_name' => $cost->receipt_payment_name,
                                        'amount_admin' => $cost->amount
                                    ];
                                }
                            if (!empty($quotaCosts))
                                $this->getRouteService()->_updateRouteCost($routeEntity, $quotaCostList);
                        }
                    }

                    $orders = $routeOrders[$routeEntity->id];
                    if ($orders != null && count($orders) > 0) {
                        if ($countCancel == count($orders))
                            $status = config('constant.status_cancel');
                        else if ($countComplete == count($orders) || ($countComplete > 0 && ($countComplete + $countCancel) == count($orders)))
                            $status = config('constant.status_complete');
                    } else {
                        $status = config('constant.status_complete');
                    }

                    $route['name'] = $routeName;
                    $route['ETD_date'] = empty($ETD_date) ? null : AppConstant::convertDate($ETD_date, 'Y-m-d');
                    $route['ETD_time'] = empty($ETD_time) ? null : AppConstant::convertTime($ETD_time, 'H:i');
                    $route['ETA_date'] = empty($ETA_date) ? null : AppConstant::convertDate($ETA_date, 'Y-m-d');
                    $route['ETA_time'] = empty($ETA_time) ? null : AppConstant::convertTime($ETA_time, 'H:i');
                    $route['location_destination_id'] = $location_destination_id;
                    $route['location_arrival_id'] = $location_arrival_id;

                    $route['ETA_date_reality'] = null;
                    $route['ETA_time_reality'] = null;
                    if ($status == config('constant.status_complete')) {
                        $route['ETA_date_reality'] = empty($ETA_date_reality) ? null : AppConstant::convertDate($ETA_date_reality, 'Y-m-d');
                        $route['ETA_time_reality'] = empty($ETA_time_reality) ? null : AppConstant::convertTime($ETA_time_reality, 'H:i');
                    }

                    $route['ETD_date_reality'] = null;
                    $route['ETD_time_reality'] = null;
                    if ($ETD_date_reality != null) {
                        $route['ETD_date_reality'] = AppConstant::convertDate($ETD_date_reality, 'Y-m-d');
                        $route['ETD_time_reality'] = AppConstant::convertTime($ETD_time_reality, 'H:i');
                    }
                    $route['route_status'] = $status;

                    $route['capacity_weight_ratio'] = 0;
                    $route['capacity_volume_ratio'] = 0;
                    if ($vehicleList[$routeEntity->vehicle_id]) {
                        $vehicle = $vehicleList[$routeEntity->vehicle_id];
                        $route['capacity_weight_ratio'] = empty($vehicle->weight) || $vehicle->weight == 0 ? 100 : round(($totalWeight / $vehicle->weight) * 100, 2);
                        $route['capacity_volume_ratio'] = empty($vehicle->volume) || $vehicle->volume == 0 ? 100 : round(($totalVolume / $vehicle->volume) * 100, 2);
                    }

                    //Lưu thông tin dư thừa trên chuyến
                    $orderArray = is_array($orders) ? $orders : $orders->toArray();
                    $orderCodes = implode(';', array_filter(array_column($orderArray, 'order_code')));
                    $orderNotes = implode('|', array_filter(array_column($orderArray, 'note')));
                    $customerIds = implode(',', array_unique(array_filter(array_column($orderArray, 'customer_id'))));
                    $vinNos = implode(';', array_filter(array_column($orderArray, 'vin_no')));
                    $modelNos = implode(';', array_filter(array_column($orderArray, 'model_no')));
                    $volume = array_sum(array_column($orderArray, 'volume'));
                    $weight = array_sum(array_column($orderArray, 'weight'));
                    $quantity = array_sum(array_column($orderArray, 'quantity'));
                    $totalAmount = array_sum(array_column($orderArray, 'amount'));

                    $route['order_codes'] = $orderCodes;
                    $route['order_notes'] = $orderNotes;
                    $route['customer_ids'] = $customerIds;
                    $route['volume'] = $volume;
                    $route['weight'] = $weight;
                    $route['quantity'] = $quantity;
                    $route['total_amount'] = $totalAmount;
                    $route['count_order'] = count($orders);
                    $route['vin_nos'] = $vinNos;
                    $route['model_nos'] = $modelNos;
                    $route['upd_id'] = $userId;

                    $routeUpdates[] = $route;
                } else {
                    //Xóa chuyến không có DH
                    $route = [];
                    $route['id'] = $routeEntity->id;
                    $route['del_flag'] = 1;
                    $route['upd_id'] = $userId;
                    $routeUpdates[] = $route;

                    $routeCost = [];
                    $routeCost['route_id'] = $routeEntity->id;
                    $routeCostDeletes[] = $routeCost;
                }
            }

            //Cập nhật chuyến
            if (!empty($routeUpdates)) {
                $routeInstance = new Routes();
                Batch::update($routeInstance, $routeUpdates, 'id');

                $routeCostInstance = new RouteCost();
                Batch::update($routeCostInstance, $routeCostDeletes, 'route_id');
            }

        }
    }
}