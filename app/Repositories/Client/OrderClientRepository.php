<?php

namespace App\Repositories\Client;

use App\Common\AppConstant;
use App\Model\Entities\Order;
use App\Repositories\OrderRepository;
use App\Validators\OrderValidator;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use stdClass;

class OrderClientRepository extends OrderRepository
{
    protected function getClientColumns($clientID, $customerID, $table_name)
    {
        $columns = [
            $table_name . '.*',
            $table_name . '.id as key',
            DB::raw('ld.title as name_of_location_destination_id'),
            DB::raw('la.title as name_of_location_arrival_id'),
            'v.id as vehicle_id',
            'v.reg_no as name_of_vehicle_id',
            'd.id as primary_driver_id',
            'c.full_name as name_of_client_id',
            'd.full_name as name_of_primary_driver_id',
        ];
        return $columns;
    }

    protected function getClientBuilder($clientID, $customerID, $table_name, $columns, $request)
    {
        $query = DB::table($table_name)
            ->leftJoin('locations as ld', $table_name . '.location_destination_id', '=', 'ld.id')
            ->leftJoin('locations as la', $table_name . '.location_arrival_id', '=', 'la.id')
            ->leftJoin('customer as c', $table_name . '.client_id', '=', 'c.id')
            ->leftJoin('vehicle as v', $table_name . '.vehicle_id', '=', 'v.id')
            ->leftJoin('drivers as d', $table_name . '.primary_driver_id', '=', 'd.id')
            ->where([
                [$table_name . '.del_flag', '=', '0']
            ])->select($columns);
        return $query;
    }

    protected function getWhereTextSearch($query, $table_name, $textSearch)
    {
        if (!empty($textSearch)) {
            $query->where(function ($query) use ($textSearch, $table_name) {
                $query->where($table_name . '.order_code', 'like', '%' . $textSearch . '%')
                    ->orWhere($table_name . '.order_no', 'like', '%' . $textSearch . '%');
            });
        }
        return $query;
    }

    public function getDataForClientByID($customerID, $id)
    {
        $item = parent::getDataForClientByID($customerID, $id);
        if ($item != null) {
            $item->list_goods = DB::table('order_goods')
                ->join('goods_type as gt', 'order_goods.goods_type_id', '=', 'gt.id')
                ->leftJoin('goods_unit as gu', 'order_goods.goods_unit_id', '=', 'gu.id')
                ->leftJoin('files', 'files.file_id', '=', 'gt.file_id')
                ->where('order_goods.order_id', '=', $id)
                ->get([
                    'order_goods.*',
                    'gt.title as name_of_goods_type_id',
                    'gu.title as name_of_goods_unit_id',
                    'files.file_name',
                    'files.file_type',
                    'files.path',
                ]);
            foreach ($item->list_goods as $goods) {
                $goods->file_path = AppConstant::getImagePath($goods->path, $goods->file_type);
            }
            $item->locations = DB::table('order_locations')
                ->join('locations', 'order_locations.location_id', '=', 'locations.id')
                ->where('order_locations.order_id', '=', $id)
                ->orderBy('order_locations.type', 'ASC')
                ->get([
                    'order_locations.*',
                    'locations.title as name_of_location_id'
                ]);
        }

        return $item;
    }

    // API xoá
    // CreatedBy nlhoang 20/05/2020
    public function deleteDataByID($id)
    {
        $item = Order::whereIn('id', $id);
        if (!is_null($item)) {
            $item->delete();
        } else {
            new Exception('Invalid location id: ' . $id);
        }
    }

    // API lấy lịch sử đơn hàng
    // CreatedBy nlhoang 03/06/2020
    public function getHistory($id)
    {
        $item = DB::table('order_history')
            ->where('order_history.order_id', '=', $id)
            ->orderBy('order_history.ins_date', 'ASC')
            ->get([
                'order_history.ins_date',
                'order_history.order_status',
                'order_history.current_location',
            ]);
        return $item;
    }

    // API lấy lộ trình đơn hàng
    // CreatedBy nlhoang 29/06/2020
    public function getRoute($id)
    {
        $ordersQuery = DB::table('orders')
            ->leftJoin(
                DB::raw('(SELECT locations.id, locations.full_address, locations.longitude, locations.latitude FROM locations) as location_destination'),
                'location_destination.id',
                '=',
                'orders.location_destination_id'
            )
            ->leftJoin(
                DB::raw('(SELECT locations.id, locations.full_address, locations.longitude, locations.latitude FROM locations) as location_arrival'),
                'location_arrival.id',
                '=',
                'orders.location_arrival_id'
            )
            ->leftJoin('vehicle', 'orders.vehicle_id', '=', 'vehicle.id')
            ->where([
                ['orders.id', '=', $id],
                ['orders.del_flag', '=', '0'],
                ['vehicle.del_flag', '=', '0']
            ]);
        $order = $ordersQuery->get([
            'orders.id as order_id', 'order_code', 'orders.status as status',
            'orders.ETD_date', 'orders.ETD_time', 'location_destination.full_address as location_destination', 'location_destination.longitude as location_destination_longitude', 'location_destination.latitude as location_destination_latitude',
            'orders.ETA_date', 'orders.ETA_time', 'location_arrival.full_address as location_arrival', 'location_arrival.longitude as location_arrival_longitude', 'location_arrival.latitude as location_arrival_latitude',
            'vehicle.latitude as current_latitude', 'vehicle.longitude as current_longitude', 'vehicle.current_location as current_location'
        ])->first();
        return $order;
    }

    public function getReviewInfo($orderId)
    {
        if (!$orderId)
            return null;
        $items = DB::table('orders as o')
            ->join('order_customer_review as oc', 'oc.id', '=', 'o.order_review_id')
            ->where('o.id', '=', $orderId)
            ->get(['oc.*']);
        foreach ($items as $review) {
            $review->files = DB::table("order_file as df")
                ->join('files', 'files.file_id', '=', 'df.file_id')
                ->where('df.order_id', '=', $orderId)
                ->where('df.del_flag', 0)
                ->where('df.order_status', '=', config("constant.FILE_REVIEW_ORDER_TYPE"))
                ->get([
                    'df.*',
                    'files.file_name',
                    'files.file_type',
                    'files.path',
                ]);
            foreach ($review->files as $file) {
                $file->file_path = AppConstant::getImagePath($file->path, $file->file_type);
            }
        }
        return $items;
    }
}
