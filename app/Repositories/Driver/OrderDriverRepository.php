<?php

namespace App\Repositories\Driver;

use App\Common\AppConstant;
use App\Model\Entities\Order;
use App\Model\Entities\RouteFile;
use App\Model\Entities\Routes;
use App\Repositories\OrderRepository;
use App\Validators\RoutesValidator;
use Carbon\Carbon;
use DB;
use Exception;
use Illuminate\Support\Facades\Auth;

class OrderDriverRepository extends OrderRepository
{

    public function countOrderStatusByToday()
    {
        // Đếm đơn hàng vào ngày hôm nay khi: nếu đơn hàng có trạng thái là hoàn thành và thời gian hoàn thành thực tế thì tgian đó phải là ngày hôm nay. Còn nếu ko thì sẽ lấy theo ETA < NOW < ETD
        $query = "SELECT tmp.status, COUNT(tmp.id) as count
                            FROM (
                                SELECT o.id, o.status FROM orders o 
                                WHERE o.del_flag = 0 AND o.status != 1 
                                AND CASE WHEN o.ETA_date_reality IS NOT NULL AND o.status = 5 
                                        THEN o.ETA_date_reality = CURDATE() 
		                                ELSE o.ETA_date = CURDATE() END
                                UNION 
                                SELECT o.id, o.status FROM orders o 
                                WHERE o.del_flag = 0 AND o.status = 1
                                AND DATE(o.ins_date) = CURDATE()
                                ) tmp
                            GROUP BY tmp.status";
        return DB::select($query);
    }

    public function getOrderNoticeForDriver()
    {
        $orderNotices = DB::table('orders as o')
            ->where([
                ["o.del_flag", "=", 0],
            ])
            ->whereNotNull("o.primary_driver_id")
            ->groupBy("o.primary_driver_id")
            ->get([
                "o.primary_driver_id as driver_id",
                DB::raw("SUM(CASE WHEN o.status = 5 AND o.ETA_date_reality = CURRENT_DATE() THEN 1 ELSE 0 END) total_complete"),
                DB::raw("SUM(CASE WHEN o.status IN (3,4,7) AND o.ETA_date <= CURRENT_DATE() THEN 1 ELSE 0 END) total_pending")
            ]);
        return $orderNotices;
    }

    //Lấy tổng điểm nhận và điểm tra của đơn hàng
    // CreatedBy nlhoang 07/08/2020
    public function getTotalOrdersOnRoute($orderIDs)
    {
        if (empty($orderIDs) || count($orderIDs) == 0) {
            return [];
        }
        $items = DB::table('orders as o')
            ->join('order_locations as ol', 'o.id', '=', 'ol.order_id')
            ->select(
                'o.id',
                DB::raw('COUNT(case when ol.type = 1 then 1 end) as total_delivery'),
                DB::raw('count(case when ol.type = 2 then 1 end) AS total_arrival')
            )
            ->groupBy('o.id')
            ->where('o.del_flag', '=', 0)
            ->whereIn('o.id', $orderIDs)
            ->get();
        return $items;
    }

    public function getItemsByRouteId($route_id)
    {
        $ordersQuery = DB::table('orders')
            ->leftJoin(
                DB::raw('(SELECT locations.id ,locations.latitude, locations.longitude, locations.full_address, locations.title FROM locations) as location_destination'),
                'location_destination.id',
                '=',
                'orders.location_destination_id'
            )
            ->leftJoin(
                DB::raw('(SELECT locations.id ,locations.latitude, locations.longitude, locations.full_address, locations.title FROM locations) as location_arrival'),
                'location_arrival.id',
                '=',
                'orders.location_arrival_id'
            )
            ->leftJoin('customer', 'orders.customer_id', 'customer.id')
            ->where('orders.del_flag', '=', 0)
            ->where('orders.route_id', $route_id);

        if (!empty($sorts)) {
            foreach ($sorts as $sort) {
                $ordersQuery->orderBy($sort['sortField'], $sort['sortType']);
            }
        }

        $orders = $ordersQuery->get([
            'orders.id as order_id',
            'orders.*',
            'location_destination.full_address as location_destination',
            'location_destination.latitude as latitude_destination',
            'location_destination.longitude as longitude_destination',
            'location_destination.title as name_of_location_destination_id',

            'location_arrival.full_address as location_arrival',
            'location_arrival.latitude as latitude_arrival',
            'location_arrival.longitude as longitude_arrival',
            'location_arrival.title as name_of_location_arrival_id',

            'orders.note as note',
            'customer.full_name as customer_full_name',
        ]);

        $locations = DB::table('orders as o')
            ->join('order_locations as ol', 'o.id', '=', 'ol.order_id')
            ->select(
                'o.id',
                DB::raw('COUNT(case when ol.type = 1 then 1 end) as total_delivery'),
                DB::raw('count(case when ol.type = 2 then 1 end) AS total_arrival')
            )
            ->groupBy('o.id')
            ->where('o.del_flag', '=', 0)
            ->where('o.route_id', $route_id)
            ->get();
        foreach ($orders as &$order) {
            $item = $locations->filter(function ($location) use ($order) {
                return $location->id == $order->order_id;
            })->first();
            if ($item != null) {
                $order->total_delivery = $item->total_delivery;
                $order->total_arrival = $item->total_arrival;
            }
        }
        return  $orders;
    }
}
