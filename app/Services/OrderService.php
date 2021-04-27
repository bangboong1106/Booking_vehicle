<?php

namespace App\Services;

use App\Common\AppConstant;
use App\Model\Entities\Order;
use App\Model\Entities\OrderGoods;
use App\Model\Entities\OrderLocation;
use App\Repositories\OrderRepository;

class OrderService
{
    protected $_orderRepository;
    protected $_routeService;

    public function __construct(OrderRepository $orderRepository,
                                RouteService $routeService)
    {
        $this->_orderRepository = $orderRepository;
        $this->_routeService = $routeService;
    }

    public function validSplitOrder($orderParent, $dataOrders)
    {
        $message = '';
        $goodsQuantityList = [];
        foreach ($dataOrders as $dataOrder) {
            if (isset($dataOrder['vehicle_id']) && (!isset($dataOrder['driver_id']) || empty($dataOrder['driver_id']))) {
                $message = 'Bạn chưa nhập tài xế';
            }
            if (isset($dataOrder['driver_id']) && (!isset($dataOrder['vehicle_id']) || empty($dataOrder['vehicle_id']))) {
                $message = 'Bạn chưa nhập xe';
            }
            $dataGoodsList = $dataOrder['goods_list'];
            if (!empty($dataGoodsList))
                foreach ($dataGoodsList as $goods_type_id => $quantity) {
                    if (!isset($goodsQuantityList[$goods_type_id])) {
                        $goodsQuantityList[$goods_type_id] = 0;
                    }
                    $goodsQuantityList[$goods_type_id] += $quantity;
                }
        }

        $goodsOrderParentList = $orderParent->listGoods->pluck('pivot');
        if ($goodsOrderParentList)
            foreach ($goodsOrderParentList as $goods) {
                if (!isset($goodsQuantityList[$goods->goods_type_id]) || $goods->quantity != $goodsQuantityList[$goods->goods_type_id]) {
                    $message = 'Số lượng hàng hóa chưa đúng .';
                }
            }

        return $message;
    }

    //Tách đơn
    public function splitOrder($orderParent, $dataOrders, $sourceCreation)
    {
        $orderGoodsList = $orderParent->listGoods->pluck('pivot');
        if (empty($orderGoodsList))
            return;

        $orderGoodsMap = [];
        foreach ($orderGoodsList as $orderGoods) {
            $orderGoodsMap[$orderGoods->goods_type_id] = $orderGoods;
        }

        foreach ($dataOrders as $dataOrder) {
            $order = new Order();
            $code = app('App\Http\Controllers\Backend\SystemCodeConfigController')->generateSystemCode(config('constant.sc_order'), null, true);
            $order->order_code = $code;
            $order->order_no = $orderParent->order_no;
            $order->customer_id = $orderParent->customer_id;
            $order->client_id = $orderParent->client_id;
            $order->customer_name = $orderParent->customer_name;
            $order->customer_mobile_no = $orderParent->customer_mobile_no;
            $order->order_date = $orderParent->order_date;
            $order->good_details = $orderParent->goods_detail;
            $order->order_customer_id = $orderParent->order_customer_id;
            $order->location_destination_id = $orderParent->location_destination_id;
            $order->ETD_date = $orderParent->ETD_date;
            $order->ETD_time = $orderParent->ETD_time;
            $order->location_arrival_id = $orderParent->location_arrival_id;
            $order->ETA_date = $orderParent->ETA_date;
            $order->ETA_time = $orderParent->ETA_time;

            $order->precedence = config('constant.ORDER_PRECEDENCE_NORMAL');
            $order->source_creation = $sourceCreation;

            $quantity = 0;
            $weight = 0;
            $volume = 0;
            $dataGoods = [];
            $dataGoodsList = $dataOrder['goods_list'];
            if (!empty($dataGoodsList))
                foreach ($dataGoodsList as $goods_type_id => $quantity) {
                    if (isset($orderGoodsMap[$goods_type_id])) {
                        $item = $orderGoodsMap[$goods_type_id];
                        $total_weight = $quantity * $item->weight;
                        $total_volume = $quantity * $item->volume;
                        $dataGoods[$goods_type_id] = [
                            'goods_type_id' => $goods_type_id,
                            'goods_unit_id' => $item->goods_unit_id,
                            'quantity' => $quantity,
                            'weight' => $item->weight,
                            'volume' => $item->volume,
                            'total_weight' => $total_weight,
                            'total_volume' => $total_volume,
                            'note' => $item->note,
                            'insured_goods' => $item->insured_goods
                        ];
                        $quantity += $quantity;
                        $weight += $total_weight;
                        $volume += $total_volume;
                    }

                }
            $order->quantity = $quantity;
            $order->weight = $weight;
            $order->volume = $volume;

            $status = config('constant.KHOI_TAO');

            if (isset($dataOrder['partner_id'])) {
                $order->partner_id = $dataOrder['partner_id'];
                $order->status_partner = config('constant.PARTNER_CHO_XAC_NHAN');
            }

            //Xử lý chuyến nếu nhập xe tài xế
            if (isset($dataOrder['vehicle_id']) && isset($dataOrder['driver_id'])) {
                $order->vehicle_id = $dataOrder['vehicle_id'];
                $order->primary_driver_id = $dataOrder['driver_id'];
                $order->status_partner = config('constant.PARTNER_XAC_NHAN');
                $status = config('constant.TAI_XE_XAC_NHAN');
                $this->_routeService->_processRouteFromOrder(1, $order, null, $dataOrder['vehicle_id'], $dataOrder['driver_id'], null);
            }

            $order->status = $status;
            $order->save();

            /*    $orderPayment = new OrderPayment();
                $orderPayment->order_id = $order->id;
                $orderPayment->payment_user_id = $orderParent->payment_user_id;
                $orderPayment->goods_amount = $orderParent->goods_amount;
                $orderPayment->vat = $orderParent->vat;
                $orderPayment->anonymous_amount = $orderParent->anonymous_amount;
                $orderPayment->save();*/

            $orderLocationDes = new OrderLocation();
            $orderLocationDes->order_id = $order->id;
            $orderLocationDes->location_id = $orderParent->location_destination_id;
            $orderLocationDes->type = config('constant.DESTINATION');
            $orderLocationDes->date = $orderParent->ETD_date;
            $orderLocationDes->time = $orderParent->ETD_time;
            $orderLocationDes->save();

            $orderLocationArrival = new OrderLocation();
            $orderLocationArrival->order_id = $order->id;
            $orderLocationArrival->location_id = $orderParent->location_arrival_id;
            $orderLocationArrival->type = config('constant.ARRIVAL');
            $orderLocationArrival->date = $orderParent->ETA_date;
            $orderLocationArrival->time = $orderParent->ETA_time;
            $orderLocationArrival->save();

            if (!empty($dataGoods))
                foreach ($dataGoods as $goods) {
                    $orderGoods = new OrderGoods();
                    $orderGoods->order_id = $order->id;
                    $orderGoods->goods_type_id = $goods['goods_type_id'];
                    $orderGoods->goods_unit_id = $goods['goods_unit_id'];
                    $orderGoods->insured_goods = $goods['insured_goods'];
                    $orderGoods->quantity = $goods['quantity'];
                    $orderGoods->weight = $goods['weight'];
                    $orderGoods->volume = $goods['volume'];
                    $orderGoods->total_weight = $goods['total_weight'];
                    $orderGoods->total_volume = $goods['total_volume'];
                    $orderGoods->note = $goods['note'];
                    $orderGoods->save();
                }
        }

        //Xóa đơn tổng
        $orderParent->delete();
    }

    //Gộp đơn
    public function mergeOrder($orders, $sourceCreation)
    {
        $ETD_date = null;
        $ETD_time = null;
        $ETA_date = null;
        $ETA_time = null;
        $location_destination_id = null;
        $location_arrival_id = null;
        $dataGoodsList = [];
        $quantity = 0;
        $weight = 0;
        $volume = 0;

        foreach ($orders as $order) {
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

            $orderGoods = $order->listGoods->pluck('pivot');

            foreach ($orderGoods as $goods) {
                if (isset($dataGoodsList[$goods->goods_type_id])) {
                    $dataGoodsList[$goods->goods_type_id]['quantity'] += $goods['quantity'];
                    $dataGoodsList[$goods->goods_type_id]['total_weight'] += $goods['total_weight'];
                    $dataGoodsList[$goods->goods_type_id]['total_volume'] += $goods['total_volume'];
                } else {
                    $dataGoodsList[$goods->goods_type_id] = [
                        'goods_type_id' => $goods->goods_type_id,
                        'goods_unit_id' => $goods->goods_unit_id,
                        'quantity' => $goods->quantity,
                        'weight' => $goods->weight,
                        'volume' => $goods->volume,
                        'total_weight' => $goods->total_weight,
                        'total_volume' => $goods->total_volume,
                        'note' => $goods->note,
                        'insured_goods' => $goods->insured_goods
                    ];
                }

                $quantity += $goods['quantity'];
                $weight += $goods['total_weight'];
                $volume += $goods['total_volume'];
            }

            //Xóa đơn con
            $order->delete();
        }

        $orderFirst = $orders[0];
        $order = $orderFirst->replicate();
        unset($order->id);
        $code = app('App\Http\Controllers\Backend\SystemCodeConfigController')->generateSystemCode(config('constant.sc_order'), null, true);
        $order->code = $code;
        $order->location_destination_id = $location_destination_id;
        $order->ETD_date = $ETD_date;
        $order->ETD_time = $ETD_time;
        $order->location_arrival_id = $location_arrival_id;
        $order->ETA_date = $ETA_date;
        $order->ETA_time = $ETA_time;

        $order->precedence = config('constant.ORDER_PRECEDENCE_NORMAL');
        $order->source_creation = $sourceCreation;

        $order->status = $orderFirst->status;
        $order->status_partner = $orderFirst->status_partner;
        $order->quantity = $quantity;
        $order->weight = $weight;
        $order->volume = $volume;
        $order->save();

        $orderLocationDes = new OrderLocation();
        $orderLocationDes->order_id = $order->id;
        $orderLocationDes->location_id = $location_destination_id;
        $orderLocationDes->type = config('constant.DESTINATION');
        $orderLocationDes->date = $ETD_date;
        $orderLocationDes->time = $ETD_time;
        $orderLocationDes->save();

        $orderLocationArrival = new OrderLocation();
        $orderLocationArrival->order_id = $order->id;
        $orderLocationArrival->location_id = $location_arrival_id;
        $orderLocationArrival->type = config('constant.ARRIVAL');
        $orderLocationArrival->date = $ETA_date;
        $orderLocationArrival->time = $ETA_time;
        $orderLocationArrival->save();

        if (!empty($dataGoodsList))
            foreach ($dataGoodsList as $goods) {
                $orderGoods = new OrderGoods();
                $orderGoods->order_id = $order->id;
                $orderGoods->goods_type_id = $goods['goods_type_id'];
                $orderGoods->goods_unit_id = $goods['goods_unit_id'];
                $orderGoods->insured_goods = $goods['insured_goods'];
                $orderGoods->quantity = $goods['quantity'];
                $orderGoods->weight = $goods['weight'];
                $orderGoods->volume = $goods['volume'];
                $orderGoods->total_weight = $goods['total_weight'];
                $orderGoods->total_volume = $goods['total_volume'];
                $orderGoods->note = $goods['note'];
                $orderGoods->save();
            }


    }

}