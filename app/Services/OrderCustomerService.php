<?php

namespace App\Services;

use App\Common\AppConstant;
use App\Common\GoogleConstant;
use App\Model\Entities\Order;
use App\Model\Entities\OrderGoods;
use App\Model\Entities\OrderLocation;
use App\Model\Entities\OrderPayment;
use App\Repositories\CustomerRepository;
use App\Repositories\LocationRepository;
use App\Repositories\OrderCustomerRepository;
use App\Repositories\OrderRepository;
use Illuminate\Support\Facades\DB;

class OrderCustomerService
{
    protected $_orderRepository;
    protected $_customerRepository;
    protected $_orderCustomerRepository;
    protected $_notificationService;
    protected $_locationRepository;

    public function __construct(OrderRepository $orderRepository,
                                CustomerRepository $customerRepository,
                                OrderCustomerRepository $orderCustomerRepository,
                                NotificationService $notificationService,
                                LocationRepository $locationRepository)
    {
        $this->_orderRepository = $orderRepository;
        $this->_customerRepository = $customerRepository;
        $this->_orderCustomerRepository = $orderCustomerRepository;
        $this->_notificationService = $notificationService;
        $this->_locationRepository = $locationRepository;
    }

    public function createOrderFromOrderCustomer($orderCustomer, $dataGoods, $sourceCreation)
    {
        $order = new Order();
        $code = app('App\Http\Controllers\Backend\SystemCodeConfigController')->generateSystemCode(config('constant.sc_order'), null, true);
        $order->order_code = $code;
        $order->order_no = $orderCustomer->order_no;
        $order->customer_id = $orderCustomer->customer_id;
        $order->client_id = $orderCustomer->client_id;
        $order->customer_name = $orderCustomer->customer_name;
        $order->customer_mobile_no = $orderCustomer->customer_mobile_no;
        $order->order_date = $orderCustomer->order_date;
        $order->good_details = $orderCustomer->goods_detail;
        $order->status = config('constant.KHOI_TAO');
        $order->order_customer_id = $orderCustomer->id;
        $order->location_destination_id = $orderCustomer->location_destination_id;
        $order->ETD_date = $orderCustomer->ETD_date;
        $order->ETD_time = $orderCustomer->ETD_time;
        $order->location_arrival_id = $orderCustomer->location_arrival_id;
        $order->ETA_date = $orderCustomer->ETA_date;
        $order->ETA_time = $orderCustomer->ETA_time;

        $order->precedence = config('constant.ORDER_PRECEDENCE_NORMAL');
        $order->source_creation = $sourceCreation;

        $quantity = 0;
        $weight = 0;
        $volume = 0;
        if (!empty($dataGoods))
            foreach ($dataGoods as $goods) {
                $quantity += isset($goods['quantity']) && is_numeric($goods['quantity']) ? $goods['quantity'] : 0;
                $weight += isset($goods['total_weight']) && is_numeric($goods['total_weight']) ? $goods['total_weight'] : 0;
                $volume += isset($goods['total_volume']) && is_numeric($goods['total_volume']) ? $goods['total_volume'] : 0;
            }
        $order->quantity = $quantity;
        $order->weight = $weight;
        $order->volume = $volume;
        $order->save();

        /* $orderPayment = new OrderPayment();
         $orderPayment->order_id = $order->id;
         $orderPayment->payment_user_id = $orderCustomer->payment_user_id;
         $orderPayment->goods_amount = $orderCustomer->goods_amount;
         $orderPayment->vat = $orderCustomer->vat;
         $orderPayment->anonymous_amount = $orderCustomer->anonymous_amount;
         $orderPayment->save();*/

        $orderLocationDes = new OrderLocation();
        $orderLocationDes->order_id = $order->id;
        $orderLocationDes->location_id = $orderCustomer->location_destination_id;
        $orderLocationDes->type = config('constant.DESTINATION');
        $orderLocationDes->date = $orderCustomer->ETD_date;
        $orderLocationDes->time = $orderCustomer->ETD_time;
        $orderLocationDes->save();

        $orderLocationArrival = new OrderLocation();
        $orderLocationArrival->order_id = $order->id;
        $orderLocationArrival->location_id = $orderCustomer->location_arrival_id;
        $orderLocationArrival->type = config('constant.ARRIVAL');
        $orderLocationArrival->date = $orderCustomer->ETA_date;
        $orderLocationArrival->time = $orderCustomer->ETA_time;
        $orderLocationArrival->save();

        if (!empty($dataGoods))
            foreach ($dataGoods as $goods) {
                $orderGoods = new OrderGoods();
                $orderGoods->order_id = $order->id;
                $orderGoods->goods_type_id = $goods['goods_type_id'];
                $orderGoods->goods_unit_id = $goods['goods_unit_id'];
                $orderGoods->insured_goods = isset($goods['insured_goods']) ? $goods['insured_goods'] : config('constant.yes');
                $orderGoods->quantity = $goods['quantity'];
                $orderGoods->weight = $goods['weight'];
                $orderGoods->volume = $goods['volume'];
                $orderGoods->total_weight = $goods['total_weight'];
                $orderGoods->total_volume = $goods['total_volume'];
                $orderGoods->note = isset($goods['note']) ? $goods['note'] : null;
                $orderGoods->save();
            }
    }

    //Cập nhật thông tin trạng thái ,ETD, ETA
    public function updateOrderCustomerInfo($orderCustomer, $updateGoodsOut = false)
    {
        $orders = $this->_orderRepository->getOrdersByOrderCustomerId($orderCustomer->id);

        $status = config('constant.ORDER_CUSTOMER_STATUS.DA_XUAT_HANG');
        $ETD_date_reality = null;
        $ETD_time_reality = null;
        $ETA_date_reality = null;
        $ETA_time_reality = null;
        $countCancel = 0;
        $countComplete = 0;
        $countTransporting = 0;

        foreach ($orders as $order) {
            if (in_array($order->status, [config('constant.CHO_NHAN_HANG'), config('constant.DANG_VAN_CHUYEN')
                , config('constant.TAI_XE_XAC_NHAN')]))
                $countTransporting++;
            if ($order->status == config('constant.HUY'))
                $countCancel++;
            if ($order->status == config('constant.HOAN_THANH'))
                $countComplete++;

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
        }

        if ($countCancel != 0 && $countCancel == count($orders)) {
            $status = config('constant.ORDER_CUSTOMER_STATUS.C20_HUY');
        } else if ($countComplete != 0 && $countComplete == count($orders)
            && $orderCustomer->status_goods = config('constant.ORDER_CUSTOMER_STATUS_GOODS.HET_HANG')) {
            $status = config('constant.ORDER_CUSTOMER_STATUS.HOAN_THANH');
        } else if ($countTransporting > 0) {
            $status = config('constant.ORDER_CUSTOMER_STATUS.DANG_VAN_CHUYEN');
        }
        $statusCurrent = $orderCustomer->status;
        $orderCustomer->status = $status;
        if ($statusCurrent != $status && in_array($status, [config('constant.ORDER_CUSTOMER_STATUS.C20_HUY'),
                config('constant.ORDER_CUSTOMER_STATUS.HOAN_THANH'), config('constant.ORDER_CUSTOMER_STATUS.DANG_VAN_CHUYEN')])) {
            $this->_notificationService->notifyToCustomerAndClient($orderCustomer);
        }

        if ($status == config('constant.ORDER_CUSTOMER_STATUS.HOAN_THANH')) {
            $orderCustomer->ETA_date_reality = AppConstant::convertDate($ETA_date_reality, 'Y-m-d');
            $orderCustomer->ETA_time_reality = AppConstant::convertTime($ETA_time_reality);
        }
        if ($ETD_date_reality) {
            $orderCustomer->ETD_date_reality = AppConstant::convertDate($ETD_date_reality, 'Y-m-d');
            $orderCustomer->ETD_time_reality = AppConstant::convertTime($ETD_time_reality);
        }
        $orderCustomer = $this->saveOrderCustomerExtend($orderCustomer, $orders);

        if ($updateGoodsOut)
            $orderCustomer = $this->updateOrderCustomerGoodsOut($orderCustomer);

        $orderCustomer->save();
    }

    public function saveOrderCustomerExtend($orderCustomer, $orders)
    {
        if ($orders) {
            $orderArray = is_array($orders) ? $orders : $orders->toArray();
            $orderCodes = implode(';', array_filter(array_column($orderArray, 'order_code')));
            $vinNos = implode(';', array_filter(array_column($orderArray, 'vin_no')));
            $modelNos = implode(';', array_filter(array_column($orderArray, 'model_no')));

            $orderCustomer->order_codes = $orderCodes;
            $orderCustomer->count_order = count($orders);
            $orderCustomer->vin_nos = $vinNos;
            $orderCustomer->model_nos = $modelNos;
        }

        return $orderCustomer;
    }

    //Cập nhật tịnh trạng, số lượng hàng đã xuất
    public function updateOrderCustomerGoodsOut($orderCustomer)
    {

        $orderList = $this->_orderRepository->getOrdersByOrderCustomerId($orderCustomer->id);
        $totalQuantity = 0;
        if ($orderList) {
            $orderGoodsList = $this->_orderRepository->getOrderGoodsByOrderIds(array_column($orderList->toArray(), 'id'));

            if ($orderGoodsList && count($orderGoodsList) > 0) {
                //Tính tổng quantity tưng hàng hóa
                $quantityGoods = [];
                foreach ($orderGoodsList as $goods) {
                    if (isset($quantityGoods[$goods->goods_type_id]))
                        $quantityGoods[$goods->goods_type_id] += $goods->quantity;
                    else
                        $quantityGoods[$goods->goods_type_id] = $goods->quantity;

                    $totalQuantity += $goods->quantity;
                }
            }
        }

        //Cập nhật quantity out dhkh
        if ($totalQuantity < $orderCustomer->quantity) {
            $orderCustomer->status_goods = config('constant.ORDER_CUSTOMER_STATUS_GOODS.CON_HANG');
        }
        $orderCustomerGoods = $orderCustomer->listGoods->pluck('pivot');
        if ($orderCustomerGoods)
            foreach ($orderCustomerGoods as $goods) {
                if (isset($quantityGoods[$goods->goods_type_id])) {
                    $goods->quantity_out = $quantityGoods[$goods->goods_type_id];
                } else {
                    $goods->quantity_out = 0;
                }
                $goods->save();
            }

        return $orderCustomer;
    }

    //Tính giá dự kiến
    public function calcAmountEstimate($locationDestinationId, $locationArrivalId, $weight)
    {
        $distance = $this->calcDistance($locationDestinationId, $locationArrivalId);
        $amountEstimate = $distance * ($weight / 1000) * 40000;

        return [$amountEstimate, $distance];
    }

    //Tính thời dan dự kiến
    public function calcETA($locationDestinationId, $locationArrivalId, $ETD)
    {
        $distance = $this->calcDistance($locationDestinationId, $locationArrivalId);
        $hourDistance = round(($distance / 25) + 6);

        $eta = date("d-m-Y H:i", strtotime('+' . $hourDistance . ' hours', strtotime($ETD)));
        return [$eta, $distance];
    }

    //Tính quãng đường của đơn
    public function calcDistance($locationDestinationId, $locationArrivalId)
    {
        $distance = 0;
        $googleConstant = new GoogleConstant(env('GOOGLE_MAP_API_KEY', ''));

        $locationDestination = $this->_locationRepository->getLocationsById($locationDestinationId);
        $locationArrival = $this->_locationRepository->getLocationsById($locationArrivalId);

        if ($locationDestination && $locationArrival) {
            // $distance = $googleConstant->calculateDistance($locationDestination->full_address, $locationArrival->full_address);
            if ($distance <= 0) {
                $locationDestinationDMS = "";
                $locationArrivalDMS = "";
                if ($locationDestination->ward_location) {
                    $locationDestinationDMS = $locationDestination->ward_location;
                } else if ($locationDestination->district_location) {
                    $locationDestinationDMS = $locationDestination->district_location;
                }

                if ($locationArrival->ward_location) {
                    $locationArrivalDMS = $locationArrival->ward_location;
                } else if ($locationArrival->district_location) {
                    $locationArrivalDMS = $locationArrival->district_location;
                }

                if (empty($locationDestinationDMS) || empty($locationArrivalDMS))
                    return $distance;

                $latLongDestination = $googleConstant->convertDMSToLatLong($locationDestinationDMS);
                $latLongArrival = $googleConstant->convertDMSToLatLong($locationArrivalDMS);

                if (!empty($latLongDestination) && !empty($latLongArrival)) {
                    $pointDestination = [
                        'lat' => $latLongDestination['latitude'],
                        'lng' => $latLongDestination['longitude']
                    ];
                    $pointArrival = [
                        'lat' => $latLongArrival['latitude'],
                        'lng' => $latLongArrival['longitude']
                    ];

                    $distance = $googleConstant->getDistanceBetween($pointDestination, $pointArrival, 'km');
                }
            }
        }

        return $distance;
    }

}