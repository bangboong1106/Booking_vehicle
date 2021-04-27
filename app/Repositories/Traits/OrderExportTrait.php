<?php

namespace App\Repositories\Traits;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use stdClass;


trait OrderExportTrait
{
    // Xuất dữ liệu bảng kê
    public function getOrderByReportFilter($filter)
    {
        $dayCondition = !empty($filter['day_condition']) ? $filter['day_condition'] : env('DAY_CONDITION_DEFAULT', 4);
        if (!empty($filter['date_from'])) {
            switch ($dayCondition) {
                case 1:
                    $filter['etd_date_from'] = $filter['date_from'];
                    break;
                case 2:
                    $filter['etd_date_reality_from'] = $filter['date_from'];
                    break;
                case 3:
                    $filter['eta_date_from'] = $filter['date_from'];
                    break;
                case 4:
                    $filter['eta_date_reality_from'] = $filter['date_from'];
                    break;
            }
        }
        if (!empty($filter['date_to'])) {
            switch ($dayCondition) {
                case 1:
                    $filter['etd_date_to'] = $filter['date_to'];
                    break;
                case 2:
                    $filter['etd_date_reality_to'] = $filter['date_to'];
                    break;
                case 3:
                    $filter['eta_date_to'] = $filter['date_to'];
                    break;
                case 4:
                    $filter['eta_date_reality_to'] = $filter['date_to'];
                    break;
            }
        }
        $ordersQuery = $this
            ->leftJoin('customer', 'orders.customer_id', '=', 'customer.id')
            ->leftJoin('admin_users', 'orders.ins_id', '=', 'admin_users.id')
            ->leftJoin(
                DB::raw('(SELECT locations.id, locations.full_address, locations.title, locations.longitude, locations.latitude FROM locations) as location_destination'),
                'location_destination.id',
                '=',
                'orders.location_destination_id'
            )
            ->leftJoin(
                DB::raw('(SELECT locations.id, locations.full_address, locations.title, locations.longitude, locations.latitude FROM locations) as location_arrival'),
                'location_arrival.id',
                '=',
                'orders.location_arrival_id'
            )
            ->leftJoin('drivers', 'orders.primary_driver_id', '=', 'drivers.id')
            ->leftJoin('vehicle', 'orders.vehicle_id', '=', 'vehicle.id')
            ->leftJoin('m_vehicle_group', 'vehicle.group_id', '=', 'm_vehicle_group.id')
            ->leftJoin('routes', 'orders.route_id', '=', 'routes.id')
            ->leftJoin('driver_vehicle_team', 'driver_vehicle_team.driver_id', '=', 'drivers.id')
            ->leftJoin('customer_group_customer as cgc', function ($join) {
                $join->on('cgc.customer_id', '=', 'customer.id')
                    ->where('cgc.del_flag', '=', 0);
            })->leftJoin('admin_users_customer_group as aucg', function ($join) {
                $join->on('aucg.customer_group_id', '=', 'cgc.customer_group_id')
                    ->where('aucg.del_flag', '=', 0);
            })
            ->where([
                ['orders.del_flag', '=', '0'],
                ['routes.del_flag', '=', '0'],
            ])
            ->where(function ($query) {
                $query->where('aucg.admin_user_id', '=', Auth::User()->id)
                    ->orWhereNull('aucg.customer_group_id');
            });

        if (!empty($filter)) {
            if (!empty($filter['status']) && $filter['status'] != -1) {
                $ordersQuery->where([['orders.status', '=', $filter['status']]]);
            } else {
                $ordersQuery->where([['orders.status', '!=', config("constant.HUY")]]);
            }
            if (!empty($filter['customer_id'])) {
                $ordersQuery->where([['customer.id', '=', $filter['customer_id']]]);
            }
            if (!empty($filter['order_date_from'])) {
                $ordersQuery->where([['orders.order_date', '>=', $filter['order_date_from']]]);
            }
            if (!empty($filter['order_date_to'])) {
                $ordersQuery->where([['orders.order_date', '<=', $filter['order_date_to']]]);
            }
            if (!empty($filter['etd_date_from'])) {
                $ordersQuery->where([['orders.etd_date', '>=', $filter['etd_date_from']]]);
            }
            if (!empty($filter['etd_date_to'])) {
                $ordersQuery->where([['orders.etd_date', '<=', $filter['etd_date_to']]]);
            }
            if (!empty($filter['etd_date_reality_from'])) {
                $ordersQuery->where([['orders.etd_date_reality', '>=', $filter['etd_date_reality_from']]]);
            }
            if (!empty($filter['etd_date_reality_to'])) {
                $ordersQuery->where([['orders.etd_date_reality', '<=', $filter['etd_date_reality_to']]]);
            }
            if (!empty($filter['eta_date_from'])) {
                $ordersQuery->where([['orders.eta_date', '>=', $filter['eta_date_from']]]);
            }
            if (!empty($filter['eta_date_to'])) {
                $ordersQuery->where([['orders.eta_date', '<=', $filter['eta_date_to']]]);
            }
            if (!empty($filter['eta_date_reality_from'])) {
                $ordersQuery->where([['orders.eta_date_reality', '>=', $filter['eta_date_reality_from']]]);
            }
            if (!empty($filter['eta_date_reality_to'])) {
                $ordersQuery->where([['orders.eta_date_reality', '<=', $filter['eta_date_reality_to']]]);
            }
            if (!empty($filter['created_date_from'])) {
                $ordersQuery->where([['orders.ins_date', '>=', $filter['created_date_from']]]);
            }
            if (!empty($filter['created_date_to'])) {
                $ordersQuery->where([['orders.ins_date', '<=', $filter['created_date_to']]]);
            }
            if (!empty($filter['updated_date_from'])) {
                $ordersQuery->where([['orders.upd_date', '>=', $filter['updated_date_from']]]);
            }
            if (!empty($filter['updated_date_to'])) {
                $ordersQuery->where([['orders.upd_date', '<=', $filter['updated_date_to']]]);
            }
            if (!empty($filter['vehicle_team_id'])) {
                $ordersQuery->where([['driver_vehicle_team.vehicle_team_id', '=', $filter['vehicle_team_id']]]);
            }
            if (!empty($filter['vehicle_id'])) {
                $ordersQuery->whereIn('vehicle.id', explode(',', $filter['vehicle_id']));
            }
            if (!empty($filter['order_code'])) {
                $ordersQuery->where([['orders.order_code', 'LIKE', '%' . $filter['order_code'] . '%']]);
            }
        }

        $ordersQuery->orderByRaw('routes.route_code ASC, orders.ETA_date ASC');

        return $ordersQuery->distinct()->select([
            'orders.id as id',
            'orders.order_code as order_code',
            'orders.order_no as order_no',
            'customer.customer_code as customer_code',
            'admin_users.username as created_by',
            DB::raw('CONCAT(location_destination.title, "\n(", location_destination.full_address,")") AS location_destination'), 'orders.bill_no',
            'orders.ETD_date_reality', 'orders.ETD_time_reality',
            DB::raw('CONCAT(location_arrival.title, "\n(", location_arrival.full_address,")") AS location_arrival'),
            'orders.ETA_date_reality',
            'orders.ETA_time_reality',
            'orders.note as note',
            'vehicle.reg_no as reg_no',
            'm_vehicle_group.name as vehicle_group_name',
            'drivers.full_name as driver_full_name',
            'orders.weight as weight',
            'orders.volume as volume',
            'orders.quantity as quantity',
            'orders.amount as amount',
            'orders.commission_amount as commission_amount',
            DB::raw('IFNULL(routes.route_code,\'Không\') as route_code'),
            DB::raw('IFNULL(routes.name,\'Chưa gán chuyến\') as route_name'),
            'routes.final_cost as route_final_cost',
            'orders.route_id as route_id',
            'drivers.id as driver_id',
            DB::raw('DATE_FORMAT(orders.order_date, "%d-%m-%Y") as created_date'),
            'orders.ETD_date', 'orders.ETD_time',
            'orders.ETA_date', 'orders.ETA_time',
            'orders.cod_amount',
            'orders.is_insured_goods',
            'orders.vin_no as vin_no',
            'orders.model_no as model_no'
        ])->with('listLocations')->get();
    }

    // Lấy dữ liệu Xe để xuất theo mẫu
    // CreatedBy nlhoang 05/04/2020
    public function getDataForTemplateByID($id, $template)
    {
        $ordersQuery = DB::table('orders')
            ->leftJoin('drivers', 'drivers.id', '=', 'orders.primary_driver_id')
            ->leftJoin('vehicle', 'orders.vehicle_id', '=', 'vehicle.id')
            ->leftJoin('m_vehicle_group', 'm_vehicle_group.id', '=', 'vehicle.group_id')
            ->leftJoin('customer', 'orders.customer_id', '=', 'customer.id')
            ->leftJoin('order_payment', 'orders.id', '=', 'order_payment.order_id')
            ->leftJoin('admin_users', 'order_payment.payment_user_id', '=', 'admin_users.id')
            ->leftJoin(
                DB::raw('(SELECT locations.id, locations.full_address, locations.latitude, locations.longitude, locations.title FROM locations) as location_destination'),
                'location_destination.id',
                '=',
                'orders.location_destination_id'
            )
            ->leftJoin(
                DB::raw('(SELECT locations.id, locations.full_address, locations.latitude, locations.longitude, locations.title FROM locations) as location_arrival'),
                'location_arrival.id',
                '=',
                'orders.location_arrival_id'
            )
            ->where([
                ['orders.id', '=', $id],
                ['orders.del_flag', '=', '0'],
            ]);
        $status = convertCaseWhenQuery(config('system.order_status'), 'orders.status', 'status');
        $precedence = convertCaseWhenQuery(config('system.order_precedences'), 'orders.precedence', 'precedence');

        $order = $ordersQuery->get([
            'orders.id as order_id',
            'order_code',
            'orders.order_no',
            'orders.bill_no',
            DB::raw($status),
            DB::raw($precedence),
            'order_date',
            'customer.customer_code',
            'customer.full_name as customer_full_name',
            'customer_name',
            'customer_mobile_no',
            'orders.ETD_date',
            'orders.ETD_time',
            'orders.ETD_date_reality',
            'orders.ETD_time_reality',
            'contact_name_destination',
            'contact_mobile_no_destination',
            'location_destination_id',
            'location_destination.full_address as location_destination',
            'location_destination.title as location_des_title', 'contact_email_destination',
            'location_destination.latitude as location_des_lat',
            'location_destination.longitude as location_des_long',
            'location_arrival.latitude as location_arr_lat',
            'location_arrival.longitude as location_arr_long',
            'orders.ETA_date',
            'orders.ETA_time',
            'orders.ETA_date_reality',
            'orders.ETA_time_reality',
            'contact_name_arrival',
            'contact_mobile_no_arrival',
            'location_arrival_id',
            'location_arrival.full_address as location_arrival',
            'location_arrival.title as location_arr_title',
            'contact_email_arrival',
            'orders.good_details',
            'orders.amount',
            'orders.quantity',
            'orders.volume',
            'orders.weight',
            'loading_arrival_fee',
            'loading_destination_fee',
            'description',
            'drivers.full_name',
            'vehicle.reg_no',
            'm_vehicle_group.name as vehicle_group',
            'orders.note',
            'orders.is_collected_documents',
            'orders.status_collected_documents',
            'orders.date_collected_documents',
            'orders.time_collected_documents',
            'orders.commission_amount',
            'orders.informative_destination',
            'orders.informative_arrival',
            DB::raw('CASE WHEN orders.commission_type = 1 THEN orders.commission_value ELSE 0 END AS commission_value'),
            DB::raw(' orders.amount - coalesce(orders.commission_amount, 0 ) - coalesce(order_payment.anonymous_amount, 0 ) as final_amount'),
            DB::raw('CASE WHEN order_payment.payment_type = 1 THEN "Chuyển khoản" ELSE "Tiền mặt" END as payment_type_title'),
            'admin_users.username as pu_username',
            DB::raw('CASE WHEN order_payment.vat = 1 THEN "Có" ELSE "Không" END as vat_title'),
            DB::raw('CASE WHEN orders.is_merge_item = 1 THEN "Có" ELSE "Không" END as is_merge_item'),
            'order_payment.goods_amount as goods_amount',
            'order_payment.anonymous_amount as anonymous_amount',
            DB::raw('CASE WHEN orders.is_insured_goods = 1 THEN "Có" ELSE "Không" END as is_insured_goods'),
            'orders.vin_no',
            'orders.model_no',
            'orders.number_of_delivery_points',
            'orders.number_of_arrival_points',
        ])->first();

        $order = $order == null ? new stdClass() : $order;

        $locationQuery = DB::table('order_locations')
            ->join('locations', 'order_locations.location_id', '=', 'locations.id')
            ->leftJoin('m_province', 'locations.province_id', '=', 'm_province.province_id')
            ->leftJoin('m_district', 'locations.district_id', '=', 'm_district.district_id')
            ->leftJoin('m_ward', 'locations.ward_id', '=', 'm_ward.ward_id')
            ->where('order_locations.order_id', '=', $id)
            ->where('order_locations.type', '=', 1);
        $order->listDestinationLocations = $locationQuery->get([
            'order_locations.location_id',
            'order_locations.date',
            'order_locations.date_reality',
            'order_locations.time',
            'order_locations.time_reality',
            'order_locations.type',
            'locations.title',
            'locations.full_address',
            'm_province.title as province_title',
            'm_district.title as district_title',
            'm_ward.title as ward_title'
        ]);

        $locationQuery = DB::table('order_locations')
            ->join('locations', 'order_locations.location_id', '=', 'locations.id')
            ->leftJoin('m_province', 'locations.province_id', '=', 'm_province.province_id')
            ->leftJoin('m_district', 'locations.district_id', '=', 'm_district.district_id')
            ->leftJoin('m_ward', 'locations.ward_id', '=', 'm_ward.ward_id')
            ->where('order_locations.order_id', '=', $id)
            ->where('order_locations.type', '=', 2);
        $order->listArrivalLocations = $locationQuery->get([
            'order_locations.location_id',
            'order_locations.date',
            'order_locations.date_reality',
            'order_locations.time',
            'order_locations.time_reality',
            'order_locations.type',
            'locations.title',
            'locations.full_address',
            'm_province.title as province_title',
            'm_district.title as district_title',
            'm_ward.title as ward_title'
        ]);


        $listGoods = DB::table('order_goods')
            ->join('goods_type', 'order_goods.goods_type_id', '=', 'goods_type.id')
            ->leftJoin('goods_unit', 'order_goods.goods_unit_id', '=', 'goods_unit.id')
            ->where('order_goods.order_id', '=', $id)
            ->select([
                'order_goods.quantity',
                'order_goods.goods_type_id',
                'order_goods.goods_unit_id',
                DB::raw('case
                    when order_goods.insured_goods = 1 then "Có"
                    else "Không" end insured_goods '),
                'order_goods.note',
                'order_goods.weight',
                'order_goods.volume',
                'order_goods.total_weight',
                'order_goods.total_volume',
                'goods_unit.title as unitTitle',
                'goods_type.title'
            ])
            ->get();

        $goods = null;
        if ($template->is_print_empty_goods == 1) {
            $goods = DB::table('goods_type as gt')->orderBy('title')
                ->get([
                    'gt.id as goods_type_id',
                    'gt.title'
                ]);
        } else {
            $goods = DB::table('goods_type as gt')
                ->join('order_goods as og', 'og.goods_type_id', '=', 'gt.id')
                ->where('og.order_id', '=', $id)
                ->distinct()
                ->orderBy('gt.title')
                ->get([
                    'gt.id as goods_type_id',
                    'gt.title'
                ]);
        }

        foreach ($goods as $key => &$good) {
            $goodsFiltered = $listGoods->filter(function ($item) use ($good) {
                return $item->goods_type_id == $good->goods_type_id;
            })->first();

            if (!empty($goodsFiltered)) {
                $goods[$key] = $goodsFiltered;
            } else {
                $good->quantity = 0;
                $good->goods_unit_id = 0;
                $good->insured_goods = "Không";
                $good->note = "";
                $good->weight = 0;
                $good->volume = 0;
                $good->total_weight = 0;
                $good->total_volume = 0;
                $good->unitTitle = "";
                $goods[$key] = $good;
            }
        }
        $order->listGoods = $goods;
        $order->listGoodsTitle = !empty($listGoods) && count($listGoods) > 0 ? implode(', ', $listGoods->pluck('title')->toArray()) : "";

        return $order;
    }
}
