<?php

namespace App\Services;

use App\Common\AppConstant;
use App\Model\Entities\Routes;
use App\Repositories\DriverRepository;
use App\Repositories\LocationRepository;
use App\Repositories\OrderRepository;
use App\Repositories\QuotaCostRepository;
use App\Repositories\RouteCostRepository;
use App\Repositories\RoutesRepository;
use App\Repositories\VehicleRepository;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class RouteService
{
    protected $_orderRepository;
    protected $_routeRepository;
    protected $_vehicleRepository;
    protected $_quotaCostRepository;
    protected $_driverRepository;
    protected $_routeCostRepository;
    protected $_locationRepository;

    public function __construct(OrderRepository $orderRepository,
                                RoutesRepository $routesRepository,
                                VehicleRepository $vehicleRepository,
                                QuotaCostRepository $quotaCostRepository,
                                DriverRepository $driverRepository,
                                RouteCostRepository $routeCostRepository,
                                LocationRepository $locationRepository)
    {
        $this->_orderRepository = $orderRepository;
        $this->_routeRepository = $routesRepository;
        $this->_vehicleRepository = $vehicleRepository;
        $this->_quotaCostRepository = $quotaCostRepository;
        $this->_driverRepository = $driverRepository;
        $this->_routeCostRepository = $routeCostRepository;
        $this->_locationRepository = $locationRepository;
    }

    /**
     * @param $action 1-Thêm mới đơn , 2-Sửa đơn
     * @param $order
     * @param $routeId
     * @param $vehicleId
     * @param $primaryDriverId
     * @param $orderOld
     */
    //Xử lý chuyến từ form đơn hàng
    public function _processRouteFromOrder($action, $order, $routeId, $vehicleId, $primaryDriverId, $orderOld)
    {
        if (empty($order))
            return;

        //HD1 : Thêm mới đơn
        if ($action == 1) {
            // Nếu đơn không được gán xe và tài xế : Không xử lý j cả
            if (empty($vehicleId) || $vehicleId == 0 || empty($primaryDriverId) || $primaryDriverId == 0) {
                return;
            }
            if ($routeId) { // Cập nhật đơn vào chuyến đã chọn
                $route = $this->_routeRepository->getItemById($routeId);
                if ($route) {
                    //Cập nhật lại thông tin chuyến
                    $this->updateRouteInfo($route);
                }
            } else {

                //Tạo chuyến mới cho đơn
                $vehicle = $this->_vehicleRepository->search(['id_eq' => $vehicleId])->first();
                $this->createNewRoute([$order], $vehicleId, $primaryDriverId, $vehicle ? $vehicle->group_id : null);
            }
        }

        //HD2 : Sửa đơn
        if ($action == 2) {
            //TH1 : Nếu đơn không được gán xe và tài xế thì xóa đơn khỏi chuyến hiện tại
            if (empty($vehicleId) || $vehicleId == 0 || empty($primaryDriverId) || $primaryDriverId == 0) {
                $routeOld = $this->_routeRepository->getItemById($orderOld->route_id);
                if ($routeOld) {
                    $this->updateRouteOld($routeOld);
                }
            } else {
                if ($routeId) {
                    // TH2 : Đơn có chọn chuyến có 2 TH :
                    // 1.Đổi xe -tài xê-chuyến khác : Xóa chuyến hiện tại , cập nhật thông tin đơn vào chuyến mới
                    // 2.Không thay đổi chuyến : Cập nhập thông tin đơn vào chuyến

                    $route = $this->_routeRepository->getItemById($routeId);
                    if ($route) {
                        //Nếu thay đổi chuyến thì xóa chuyến cũ
                        $routeOld = $this->_routeRepository->getItemById($orderOld->route_id);
                        if ($routeOld && $routeOld->id != $routeId) {
                            $this->updateRouteOld($routeOld);
                        }
                        //Cập nhật lại thông tin chuyến mới
                        $this->updateRouteInfo($route);
                    }
                } else {
                    // TH3: Nếu đơn chưa chọn chuyến và được gán xe-tài xế có 3 TH:
                    // 1.Nếu đơn đã có chuyến : Cập nhật lại thông tin chuyến
                    // 2.Nếu đơn đã có chuyến nhưng xe-tài xế của chuyến khác xe-tài xế của đơn : Xóa đơn khỏi chuyến hiện tại và tạo chuyến mới cho đơn
                    // 3.Nếu đơn chưa có chuyến : Tạo chuyến mới cho đơn.

                    $routeOld = $this->_routeRepository->getItemById($orderOld->route_id);
                    if ($routeOld) {
                        if ($routeOld->vehicle_id == $vehicleId && $routeOld->driver_id == $primaryDriverId) { //TH3.1
                            $this->updateRouteInfo($routeOld);
                            return;
                        } else { //TH3.2
                            $this->updateRouteOld($routeOld);
                        }
                    }

                    //Tạo chuyến mới cho đơn
                    $vehicle = $this->_vehicleRepository->search(['id_eq' => $vehicleId])->first();
                    $this->createNewRoute([$order], $vehicleId, $primaryDriverId, $vehicle ? $vehicle->group_id : null);
                }
            }
        }
    }

    public function createNewRoute($orders, $vehicle_id, $driver_id, $vehicle_group_id)
    {
        $routeEntity = $this->_routeRepository->findFirstOrNew([]);
        $code = null;
        if (env('GENERATE_ROUTE_CODE', 1) == 0)
            $code = $orders && count($orders) > 0 ? $orders[0]->order_code : Str::random(8);
        else {
            $code = app('App\Http\Controllers\Backend\SystemCodeConfigController')->generateSystemCode(config('constant.sc_route'), null, true);
        }
        $routeEntity->route_code = $code;
        $routeEntity->vehicle_id = $vehicle_id;
        $routeEntity->driver_id = $driver_id;
        $routeEntity->save();

        $ETD_date = null;
        $ETD_time = null;
        $ETA_date = null;
        $ETA_time = null;
        $ETD_date_reality = null;
        $ETD_time_reality = null;
        $ETA_date_reality = null;
        $ETA_time_reality = null;

        $route_location_destination_id = null;
        $route_location_destination_title = null;
        $route_location_arrival_id = null;
        $route_location_arrival_title = null;
        $routeName = '';

        $countCancel = 0;
        $countComplete = 0;
        $status = config('constant.status_incomplete');

        $totalWeight = 0;
        $totalVolume = 0;
        if ($orders != null) {
            foreach ($orders as $i => $order) {
                $location_destination_title = '';
                if ($order->locationDestination)
                    $location_destination_title = $order->locationDestination->title;

                $location_arrival_title = '';
                if ($order->locationArrival)
                    $location_arrival_title = $order->locationArrival->title;

                if ($order->ETD_date != null) {
                    $dateTime1 = $order->ETD_date . ' ' . ($order->ETD_time ? $order->ETD_time : '');
                    $dateTime2 = $ETD_date . ' ' . ($ETD_time ? $ETD_time : '');
                    if ($ETD_date == null || AppConstant::isDate2GreatDate1($dateTime1, $dateTime2)) {
                        $ETD_date = $order->ETD_date;
                        $ETD_time = $order->ETD_time;
                        $route_location_destination_id = $order->location_destination_id;
                        $route_location_destination_title = $location_destination_title;
                    }
                }
                if ($order->ETA_date != null) {
                    $dateTime2 = $order->ETA_date . ' ' . ($order->ETA_time ? $order->ETA_time : '');
                    $dateTime1 = $ETA_date . ' ' . ($ETA_time ? $ETA_time : '');
                    if ($ETA_date == null || AppConstant::isDate2GreatDate1($dateTime1, $dateTime2)) {
                        $ETA_date = $order->ETA_date;
                        $ETA_time = $order->ETA_time;
                        $route_location_arrival_id = $order->location_arrival_id;
                        $route_location_arrival_title = $location_arrival_title;
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

                if ($order->status == config('constant.HUY'))
                    $countCancel++;
                if ($order->status == config('constant.HOAN_THANH'))
                    $countComplete++;

                $totalWeight += isset($order->weight) && is_numeric($order->weight) ? $order->weight : 0;
                $totalVolume += isset($order->volume) && is_numeric($order->volume) ? $order->volume : 0;

                $routeEntity->partner_id = $order->partner_id;
            }
        }

        $routeName .= $route_location_destination_title . "-" . $route_location_arrival_title;

        $quota = app('App\Http\Controllers\Backend\QuotaController')->findFirsOrNewQuotaByLocation(
            $route_location_destination_id,
            $route_location_destination_title,
            $route_location_arrival_id,
            $route_location_arrival_title,
            $vehicle_group_id
        );
        if ($quota) {
            $routeEntity->quota_id = $quota->id;

            $quotaCost = $this->_quotaCostRepository->getCosts($quota->id);
            if ($quotaCost)
                foreach ($quotaCost as $cost) {
                    $routeCostEntity = $this->_quotaCostRepository->findFirstOrNew([]);
                    $routeCostEntity->route_id = $routeEntity->id;
                    $routeCostEntity->receipt_payment_id = $cost->receipt_payment_id;
                    $routeCostEntity->receipt_payment_name = $cost->receipt_payment_name;
                    $routeCostEntity->amount_admin = $cost->amount;
                    $routeCostEntity->save();
                }
        }

        if ($orders != null && count($orders) > 0) {
            if ($countCancel == count($orders))
                $status = config('constant.status_cancel');
            else if (
                $countComplete == count($orders) ||
                ($countComplete > 0 && ($countComplete + $countCancel) == count($orders))
            )
                $status = config('constant.status_complete');
        } else {
            $status = config('constant.status_complete');
        }

        $routeEntity->name = $routeName;
        $routeEntity->route_status = $status;
        $routeEntity->ETD_date = $ETD_date;
        $routeEntity->ETD_time = $ETD_time;
        $routeEntity->ETA_date = $ETA_date;
        $routeEntity->ETA_time = $ETA_time;
        $routeEntity->location_destination_id = $route_location_destination_id;
        $routeEntity->location_arrival_id = $route_location_arrival_id;

        if ($status == config('constant.status_complete')) {
            $routeEntity->ETA_date_reality = $ETA_date_reality;
            $routeEntity->ETA_time_reality = $ETA_time_reality;
        }
        if ($ETD_date_reality != null) {
            $routeEntity->ETD_date_reality = $ETD_date_reality;
            $routeEntity->ETD_time_reality = $ETD_time_reality;
        }

        $vehicle = $this->_vehicleRepository->search(['id_eq' => $vehicle_id])->first();
        if (!empty($vehicle)) {
            $routeEntity->capacity_weight_ratio = empty($vehicle->weight) || $vehicle->weight == 0 ? 100 : round(($totalWeight / $vehicle->weight) * 100, 2);
            $routeEntity->capacity_volume_ratio = empty($vehicle->volume) || $vehicle->volume == 0 ? 100 : round(($totalVolume / $vehicle->volume) * 100, 2);
        }

        //Lưu thông tin dư thừa trên chuyến
        $routeEntity = $this->saveRouteExtend($routeEntity, $orders);

        $routeEntity->save();

        //Cập nhật lại thông tin chuyến vào đơn hàng
        foreach ($orders as $order) {
            $order->route_id = $routeEntity->id;
            $order->save();
        }
    }

    /**
     * @param $action 1: Thêm mới đơn , 2-Cập nhật đơn
     * @param $dataList
     * @param bool $fromEditor
     */
    //Xử lý chuyến từ excel
    public function _processRouteFromExcel($action, $dataList, $fromEditor = false)
    {
        $routeNewList = [];
        $orderRoutes = [];
        $routes = [];
        if ($dataList) {
            $vehicles = $this->_vehicleRepository->search()->pluck('id', 'reg_no');
            $drivers = $this->_driverRepository->search()->pluck('id', 'code');

            foreach ($dataList as $data) {
                if (!$data['importable'])
                    continue;

                //Thêm mới : Ko nhập xe , tài xế ko tạo chuyến
                if ($action == 1 && (empty($data['vehicle']) || empty($data['primary_driver'])))
                    continue;

                $data['vehicle_id'] = $fromEditor ? $data['vehicle'] : (isset($vehicles[$data['vehicle']]) ? $vehicles[$data['vehicle']] : 0);
                $data['primary_driver_id'] = $fromEditor ? $data['primary_driver'] : (isset($drivers[$data['primary_driver']]) ? $drivers[$data['primary_driver']] : 0);
                $data['secondary_driver_id'] = $fromEditor ? $data['secondary_driver'] : (isset($data['secondary_driver']) && isset($drivers[$data['secondary_driver']]) ? $drivers[$data['secondary_driver']] : 0);

                //Cập nhật : Nếu đơn không được gán xe và tài xế thì xóa đơn khỏi chuyến hiện tại
                if ($action == 2 & ($data['vehicle_id'] == 0 || $data['primary_driver_id'] == 0)) {
                    $orderRoutes[$data['order_code']] = null;
                    continue;
                }

                if (!empty($data['route_name']) && array_key_exists($data['route_name'], $routes)) {
                    $routes[$data['route_name']][] = $data;
                } else {
                    $routes[$data['order_code']][] = $data;
                }
            }
        }

        //HD1: Thêm mới đơn : Tạo chuyến tự động cho đơn đã được gán xe-tài xế
        if ($action == 1) {
            foreach ($routes as $key => $orders) {
                try {
                    if (count($orders) < 0 || (count($orders) == 1 && $orders[0]['is_merge_item'] == config('constant.yes')))
                        break;

                    $orderFirst = $orders[0];
                    $vehicleId = $orderFirst['vehicle_id'];
                    $primaryDriverId = $orderFirst['primary_driver_id'];

                    $route = $this->createNewRouteExcel($orders, $vehicleId, $primaryDriverId);
                    $routeNewList[] = $route;

                    foreach ($orders as $order) {
                        $orderRoutes[$order['order_code']] = $route['ref_id'];
                    }

                } catch (Exception $e) {
                    logError($e . ' - Route : ' . json_encode($routes));
                }
            }
        }

        //HD2: Cập nhật đơn : Cập nhật lại thông tin chuyến của đơn.
        // Những đơn để trống hoặc vẫn đang để tên chuyến thì là đơn ko dc ghép lại
        // Chuyến dc ghép lại là chuyến có 2 đơn trở lên
        if ($action == 2) {
            foreach ($routes as $key => $orders) {
                try {

                    $orderFirst = $orders[0];
                    $vehicleId = $orderFirst['vehicle_id'];
                    $primaryDriverId = $orderFirst['primary_driver_id'];

                    if (count($orders) == 1) { //Ko ghép chuyến
                        $order = $orders[0];

                        // TH2.Nếu đơn đã có chuyến : Cập nhật lại thông tin cho chuyến
                        // TH3.Nếu đơn đã có chuyến nhưng xe-tài xế của chuyến khác xe-tài xế của đơn : Xóa đơn khỏi chuyến hiện tại và tạo chuyến mới cho đơn
                        // TH4.Nếu đơn chưa có chuyến : Tạo chuyến mới cho đơn.

                        if ($order['current_route_id'] && $order['current_route_id'] != 0) {
                            if ($order['current_vehicle_id'] == $vehicleId && $order['current_primary_driver_id'] == $primaryDriverId)
                                //TH1
                                continue;
                        } else {
                            //Nếu đơn ko dc ghép và là hàng ghép, và chưa có chuyến cũ thì ko tạo chuyên
                            if ($order['is_merge_item'] == config('constant.yes'))
                                continue;
                        }

                        $route = $this->createNewRouteExcel([$order], $vehicleId, $primaryDriverId);
                        $routeNewList[] = $route;

                        //Gán đơn vào chuyến mới
                        $orderRoutes[$order['order_code']] = $route['ref_id'];

                    } else { // Đơn được ghép lại chuyến
                        //Tạo chuyến mới
                        $route = $this->createNewRouteExcel($orders, $vehicleId, $primaryDriverId);
                        $routeNewList[] = $route;

                        //Gán đơn vào chuyến mới
                        foreach ($orders as $order) {
                            $orderRoutes[$order['order_code']] = $route['ref_id'];
                        }
                    }
                } catch (Exception $e) {
                    logError($e . ' - Route : ' . json_encode($routes));
                }
            }
        }

        //Set mã chuyến
        $countRoute = count($routeNewList);
        if ($countRoute > 0) {
            $routeEntity = new Routes();
            $lastId = $routeEntity->getAutoIncrementId($countRoute);
            if ($lastId > 0) {
                $systemCodeList = app('App\Http\Controllers\Backend\SystemCodeConfigController')->generateSystemCodeForExcels(config('constant.sc_route'), $countRoute, true);
                foreach ($routeNewList as $index => &$route) {
                    $route['id'] = $lastId;
                    $route['route_code'] = isset($systemCodeList[$index]) ? $systemCodeList[$index] : Str::random(8);

                    foreach ($orderRoutes as $key => &$value) {
                        if ($value == $route['ref_id']) {
                            $orderRoutes[$key] = $route['id'];
                        }
                    }
                    $lastId++;
                    unset($route['ref_id']);
                }
            }
        }

        return [$routeNewList, $orderRoutes];
    }

    public function createNewRouteExcel($orders, $vehicle_id, $driver_id)
    {
        $route = [];
        $route['ref_id'] = Str::random(8);
        $route['vehicle_id'] = $vehicle_id;
        $route['driver_id'] = $driver_id;
        $route['ins_id'] = Auth::user()->id;
        $route['del_flag'] = "0";

        $ETD_date = null;
        $ETD_time = null;
        $ETA_date = null;
        $ETA_time = null;
        $ETD_date_reality = null;
        $ETD_time_reality = null;
        $ETA_date_reality = null;
        $ETA_time_reality = null;

        $route_location_destination_id = null;
        $route_location_destination_title = null;
        $route_location_arrival_id = null;
        $route_location_arrival_title = null;

        $countCancel = 0;
        $countComplete = 0;
        $status = config('constant.status_incomplete');

        if ($orders != null) {
            foreach ($orders as $i => $order) {

                if ($order['ETD_date'] != null) {
                    $dateTime1 = $order['ETD_date'] . ' ' . ($order['ETD_time'] ? $order['ETD_time'] : '');
                    $dateTime2 = $ETD_date . ' ' . ($ETD_time ? $ETD_time : '');
                    if ($ETD_date == null || AppConstant::isDate2GreatDate1($dateTime1, $dateTime2)) {
                        $ETD_date = $order['ETD_date'];
                        $ETD_time = $order['ETD_time'];
                        $route_location_destination_id = $order['location_destination_id'];
                        $route_location_destination_title = $order['location_destination_title'];
                    }
                }
                if ($order['ETA_date'] != null) {
                    $dateTime2 = $order['ETA_date'] . ' ' . ($order['ETA_time'] ? $order['ETA_time'] : '');
                    $dateTime1 = $ETA_date . ' ' . ($ETA_time ? $ETA_time : '');
                    if ($ETA_date == null || AppConstant::isDate2GreatDate1($dateTime1, $dateTime2)) {
                        $ETA_date = $order['ETA_date'];
                        $ETA_time = $order['ETA_time'];
                        $route_location_arrival_id = $order['location_arrival_id'];
                        $route_location_arrival_title = $order['location_arrival_title'];
                    }
                }

                if ($order['status'] == config('constant.HOAN_THANH') && $order['ETA_date_reality'] != null) {
                    $dateTime2 = $order['ETA_date_reality'] . ' ' . ($order['ETA_time_reality'] ? $order['ETA_time_reality'] : '');
                    $dateTime1 = $ETA_date_reality . ' ' . ($ETA_time_reality ? $ETA_time_reality : '');
                    if ($ETA_date_reality == null || AppConstant::isDate2GreatDate1($dateTime1, $dateTime2)) {
                        $ETA_date_reality = $order['ETA_date_reality'];
                        $ETA_time_reality = $order['ETA_time_reality'];
                    }
                }
                if ($order['ETD_date_reality'] != null) {
                    $dateTime1 = $order['ETD_date_reality'] . ' ' . ($order['ETD_time_reality'] ? $order['ETD_time_reality'] : '');
                    $dateTime2 = $ETD_date_reality . ' ' . ($ETD_time_reality ? $ETD_time_reality : '');
                    if ($ETD_date_reality == null || AppConstant::isDate2GreatDate1($dateTime1, $dateTime2)) {
                        $ETD_date_reality = $order['ETD_date_reality'];
                        $ETD_time_reality = $order['ETD_time_reality'];
                    }
                }

                if ($order['status'] == config('constant.HUY'))
                    $countCancel++;
                if ($order['status'] == config('constant.HOAN_THANH'))
                    $countComplete++;
            }
        }

        $routeName = $route_location_destination_title . "-" . $route_location_arrival_title;

        if ($orders != null && count($orders) > 0) {
            if ($countCancel == count($orders))
                $status = config('constant.status_cancel');
            else if (
                $countComplete == count($orders) ||
                ($countComplete > 0 && ($countComplete + $countCancel) == count($orders))
            )
                $status = config('constant.status_complete');
        } else {
            $status = config('constant.status_complete');
        }

        $route['name'] = $routeName;
        $route['route_status'] = $status;
        $route['ETD_date'] = empty($ETD_date) ? null : AppConstant::convertDate($ETD_date, 'Y-m-d');
        $route['ETD_time'] = empty($ETD_time) ? null : AppConstant::convertDate($ETD_time, 'H:i');
        $route['ETA_date'] = empty($ETA_date) ? null : AppConstant::convertDate($ETA_date, 'Y-m-d');
        $route['ETA_time'] = empty($ETA_time) ? null : AppConstant::convertDate($ETA_time, 'H:i');
        $route['location_destination_id'] = $route_location_destination_id;
        $route['location_arrival_id'] = $route_location_arrival_id;

        $route['ETA_date_reality'] = null;
        $route['ETA_time_reality'] = null;
        if ($status == config('constant.status_complete')) {
            $route['ETA_date_reality'] = empty($ETA_date_reality) ? null : AppConstant::convertDate($ETA_date_reality, 'Y-m-d');
            $route['ETA_time_reality'] = empty($ETA_time_reality) ? null : AppConstant::convertDate($ETA_time_reality, 'H:i');
        }

        $route['ETD_date_reality'] = null;
        $route['ETD_time_reality'] = null;
        if ($ETD_date_reality != null) {
            $route['ETD_date_reality'] = AppConstant::convertDate($ETD_date_reality, 'Y-m-d');
            $route['ETD_time_reality'] = AppConstant::convertDate($ETD_time_reality, 'H:i');
        }

        return $route;
    }

    public function saveRouteExtend($route, $orders)
    {
        if ($orders) {
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

            $route->order_codes = $orderCodes;
            $route->order_notes = $orderNotes;
            $route->customer_ids = empty($customerIds) ? $customerIds : 0;
            $route->volume = $volume;
            $route->weight = $weight;
            $route->quantity = $quantity;
            $route->total_amount = $totalAmount;
            $route->count_order = count($orders);
            $route->vin_nos = $vinNos;
            $route->model_nos = $modelNos;
        }

        return $route;
    }

    /**
     * @param $action 1-Gán xe , 2-Chuyển xe , 3-Thay đổi thời gian , 4-Xóa đơn khỏi xe
     * @param $order
     * @param $vehicleId
     * @param $primaryDriverId
     * @param $orderOld
     */
    //Xử lý chuyến từ dashboard
    public function _processRouteFromDashboard($action, $order, $vehicleId, $primaryDriverId, $orderOld)
    {
        if (empty($order))
            return;

        // HD1 : Gán xe cho đơn hàng : Tạo chuyến mới cho đơn
        if ($action == 1) {
            if ($vehicleId && $vehicleId != 0 && $primaryDriverId && $primaryDriverId != 0) {
                $vehicle = $this->_vehicleRepository->search(['id_eq' => $vehicleId])->first();
                $this->createNewRoute([$order], $vehicleId, $primaryDriverId, $vehicle ? $vehicle->group_id : null);
            }
        }

        //HD2 : Đơn hàng được gán xe-tài xế khác : Xóa đơn khỏi chuyến hiện tại và tạo chuyến mới
        if ($action == 2) {
            if ($vehicleId && $vehicleId != 0 && $primaryDriverId && $primaryDriverId != 0) {

                // Xóa đơn khỏi chuyến cũ
                $routeOld = $this->_routeRepository->getItemById($orderOld->route_id);
                if ($routeOld) {
                    $this->updateRouteOld($routeOld);
                }

                //Tạo chuyến mới cho đơn
                $vehicle = $this->_vehicleRepository->search(['id_eq' => $vehicleId])->first();
                $this->createNewRoute([$order], $vehicleId, $primaryDriverId, $vehicle ? $vehicle->group_id : null);
            }
        }

        //HD3: Thay đổi ngày giờ đơn hàng : Cập nhật lại thông tin ngày giờ của chuyến hiện tại
        if ($action == 3) {
            $route = $this->_routeRepository->getItemById($order->route_id);
            if ($route) {
                $this->updateRouteInfo($route);
            }
        }

        //HD4: Xóa đơn khỏi xe : Xóa đơn khỏi chuyến hiện tại
        if ($action == 4) {
            $routeOld = $this->_routeRepository->getItemById($orderOld->route_id);
            if ($routeOld) {
                $this->updateRouteOld($routeOld);
            }
        }
    }

    // Xử lý chuyến khi xóa đơn thì xóa đơn khỏi chuyến
    public function _processRouteFromOrderDelete($order)
    {
        $route = $this->_routeRepository->getItemById($order->route_id);
        if ($route) {
            $this->updateRouteOld($route);
        }
    }

    //Xử lý chuyến cũ
    public function updateRouteOld($routeOld)
    {
        $routeOrders = $this->_orderRepository->getOrdersByRouteId($routeOld->id);
        //Xóa chuyến nếu chuyến không có đơn hàng
        if ($routeOrders == null || count($routeOrders) == 0) {
            $this->_routeCostRepository->deleteWhere([
                'route_id' => $routeOld->id
            ]);
            $routeOld->delete();
        } else
            $this->updateRouteInfo($routeOld);
    }

    //Cập nhật ETD,ETA, ETA_reality, status của chuyến
    public function updateRouteInfo($route)
    {
        if ($route) {

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

            $status = config('constant.status_incomplete');
            $orders = $this->_orderRepository->getOrdersByRouteId($route->id);
            $vehicle = $this->_vehicleRepository->search(['id_eq' => $route->vehicle_id])->first();

            $countCancel = 0;
            $countComplete = 0;
            foreach ($orders as $i => $order) {
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

            $routeName = '';
            $locationDes = $this->_locationRepository->getLocationsById($location_destination_id);
            $routeName .= $locationDes ? $locationDes->title : "";
            $locationArr = $this->_locationRepository->getLocationsById($location_arrival_id);
            $routeName .= "-" . ($locationArr ? $locationArr->title : "");

            //Cập nhật route cost nếu thay đổi điểm đầu , điểm cuối của chuyến nếu chuyến chưa phê duyệt
            if ($route->is_approved != config('constant.DA_PHE_DUYET')
                && ($route->location_destination_id != $location_destination_id
                    || $route->location_arrival_id != $location_arrival_id)) {

                // Tạo chi phí cho chuyến
                $vehicle = $this->_vehicleRepository->search(['id_eq' => $route->vehicle_id])->first();
                $quota = app('App\Http\Controllers\Backend\QuotaController')->findFirsOrNewQuotaByLocation(
                    $location_destination_id,
                    $locationDes ? $locationDes->title : "",
                    $location_arrival_id,
                    $locationArr ? $locationArr->title : "",
                    $vehicle ? $vehicle->group_id : null
                );
                if ($quota && $quota->id != $route->quota_id) {
                    $route->quota_id = $quota->id;

                    $quotaCosts = $this->_quotaCostRepository->getCosts($quota->id);
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
                        $this->_updateRouteCost($route, $quotaCostList);
                }
            }

            if ($orders != null && count($orders) > 0) {
                if ($countCancel == count($orders))
                    $status = config('constant.status_cancel');
                else if ($countComplete == count($orders) || ($countComplete > 0 && ($countComplete + $countCancel) == count($orders)))
                    $status = config('constant.status_complete');
            } else {
                $status = config('constant.status_complete');
            }

            $route->name = $routeName;
            $route->ETD_date = $ETD_date;
            $route->ETD_time = $ETD_time;
            $route->ETA_date = $ETA_date;
            $route->ETA_time = $ETA_time;
            $route->location_destination_id = $location_destination_id;
            $route->location_arrival_id = $location_arrival_id;
            if ($status == config('constant.status_complete')) {
                $route->ETA_date_reality = $ETA_date_reality;
                $route->ETA_time_reality = $ETA_time_reality;
            }
            if ($ETD_date_reality != null) {
                $route->ETD_date_reality = $ETD_date_reality;
                $route->ETD_time_reality = $ETD_time_reality;
            }
            $route->route_status = $status;

            if (!empty($vehicle)) {
                $route->capacity_weight_ratio = empty($vehicle->weight) || $vehicle->weight == 0 ? 100 : round(($totalWeight / $vehicle->weight) * 100, 2);
                $route->capacity_volume_ratio = empty($vehicle->volume) || $vehicle->volume == 0 ? 100 : round(($totalVolume / $vehicle->volume) * 100, 2);
            }

            //Lưu thông tin dư thừa trên chuyến
            $route = $this->saveRouteExtend($route, $orders);

            $route->save();
        }
    }

    //Cập nhật cost cho chuyến xe khi thay đổi bảng định mức
    public function _processRouteFromQuota($quotaId, $quotaCosts)
    {
        $routes = $this->_routeRepository->getRouteWithQuotaID($quotaId);
        if ($routes) {
            foreach ($routes as $route) {
                if ($route->is_approved == config('constant.DA_PHE_DUYET'))
                    continue;
                if (isset($quotaCosts)) {
                    foreach ($quotaCosts as &$cost) {
                        $cost['amount_admin'] = $cost['amount'];
                    }
                }
                $this->_updateRouteCost($route, $quotaCosts);
            }
        }
    }

    /**
     * Cập nhật lai bản ghi route_cost trong bang route_cost
     * TH1: Cập nhật amount_admin nếu trùng receipt_payment_id vs BDM mới
     * TH2: Xóa route_cost nếu ko trùng receipt_payment_id vs  BDM mới và amount_driver = 0 hoặc null
     * TH3: Thêm route_cost mới nếu ko có receipt_payment_id
     * TH4: Ko làm j vs route_cost có amount_driver != 0
     * @param $routeEntity
     * @param $quotaCosts
     */
    public function _updateRouteCost($routeEntity, $quotaCosts)
    {
        if (isset($quotaCosts)) {
            $currentCosts = $routeEntity->costs->pluck('amount_driver', 'receipt_payment_id')->toArray();

            foreach ($currentCosts as $key => $amount) {
                if (isset($amount) && $amount != 0)
                    unset($currentCosts[$key]);
            }

            foreach ($quotaCosts as $cost) {
                $receipt_payment_id = empty($cost['receipt_payment_id']) ? 0 : $cost['receipt_payment_id'];

                if (array_key_exists($receipt_payment_id, $currentCosts)) {
                    //Lay list cost cần xóa cho TH2
                    unset($currentCosts[$receipt_payment_id]);
                }

                //TH1-TH3
                $routeCostEntity = $this->_routeCostRepository->getCost($routeEntity->id, $receipt_payment_id);
                if (empty($routeCostEntity))
                    $routeCostEntity = $this->_routeCostRepository->findFirstOrNew([]);

                $routeCostEntity->route_id = $routeEntity->id;
                $routeCostEntity->receipt_payment_id = $cost['receipt_payment_id'];
                $routeCostEntity->receipt_payment_name = isset($cost['receipt_payment_name']) ? $cost['receipt_payment_name'] : '';
                $routeCostEntity->amount_admin = (float)$cost['amount_admin'];
                $routeCostEntity->save();
            }

            if (!empty($currentCosts)) { //TH2
                $listCurrentCost = $this->_routeCostRepository->search([
                    'route_id_eq' => $routeEntity->id,
                    'receipt_payment_id_in' => array_keys($currentCosts)
                ])->get();
                foreach ($listCurrentCost as $costItem) {
                    $costItem->delete();
                }
            }
        }
    }

}