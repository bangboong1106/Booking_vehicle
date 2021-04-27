<?php

namespace App\Repositories\Traits;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\DB;
use stdClass;


trait OrderCustomerExportTrait
{
    public function getDataByReportFilter($filter)
    {
        $ordersQuery = $this
            ->leftJoin('customer as c', 'order_customer.customer_id', '=', 'c.id')
            ->leftJoin('locations as ld', 'order_customer.location_destination_id', '=', 'ld.id')
            ->leftJoin('locations as la', 'order_customer.location_arrival_id', '=', 'la.id')
            ->leftJoin('order_customer_vehicle_group as ocvg', 'order_customer.id', '=', 'ocvg.order_customer_id')
            ->leftJoin('m_vehicle_group as vg', 'ocvg.vehicle_group_id', '=', 'vg.id');

        if (!empty($filter)) {
            if (!empty($filter['status'])) {
                if ($filter['status'] == 5) {
                    $ordersQuery->where([['order_customer.status', '=', 1]]);
                } else if ($filter['status'] == 6) {
                    $ordersQuery->where([['order_customer.status', '=', 2]]);
                } else if ($filter['status'] == 5) {
                    $ordersQuery->where([['order_customer.status', '=', 0]]);
                }
            }
            if (!empty($filter['customer_id'])) {
                $ordersQuery->where([['c.id', '=', $filter['customer_id']]]);
            }
            if (!empty($filter['order_date_from'])) {
                $ordersQuery->where([['order_customer.order_date', '>=', $filter['order_date_from']]]);
            }
            if (!empty($filter['order_date_to'])) {
                $ordersQuery->where([['order_customer.order_date', '<=', $filter['order_date_to']]]);
            }
            if (!empty($filter['etd_date_from'])) {
                $ordersQuery->where([['order_customer.etd_date', '>=', $filter['etd_date_from']]]);
            }
            if (!empty($filter['etd_date_to'])) {
                $ordersQuery->where([['order_customer.etd_date', '<=', $filter['etd_date_to']]]);
            }
            if (!empty($filter['eta_date_from'])) {
                $ordersQuery->where([['order_customer.eta_date', '>=', $filter['eta_date_from']]]);
            }
            if (!empty($filter['eta_date_to'])) {
                $ordersQuery->where([['order_customer.eta_date', '<=', $filter['eta_date_to']]]);
            }
            if (!empty($filter['eta_date_reality_from'])) {
                $ordersQuery->where([['order_customer.eta_date_reality', '>=', $filter['eta_date_reality_from']]]);
            }
            if (!empty($filter['eta_date_reality_to'])) {
                $ordersQuery->where([['order_customer.eta_date_reality', '<=', $filter['eta_date_reality_to']]]);
            }
            if (!empty($filter['created_date_from'])) {
                $ordersQuery->where([['order_customer.ins_date', '>=', $filter['created_date_from']]]);
            }
            if (!empty($filter['created_date_to'])) {
                $ordersQuery->where([['order_customer.ins_date', '<=', $filter['created_date_to']]]);
            }
            if (!empty($filter['updated_date_from'])) {
                $ordersQuery->where([['order_customer.upd_date', '>=', $filter['updated_date_from']]]);
            }
            if (!empty($filter['updated_date_to'])) {
                $ordersQuery->where([['order_customer.upd_date', '<=', $filter['updated_date_to']]]);
            }
        }

        $ordersQuery->groupBy('order_customer.id')->orderBy('order_customer.id', 'desc');

        return $ordersQuery->distinct()->select([
            'order_customer.*',
            DB::raw('CONCAT(c.customer_code, "|", c.full_name) AS customer_full_name'),
            DB::raw('CONCAT(ld.title, "\n(", ld.full_address,")") AS location_destination'),
            DB::raw('CONCAT(la.title, "\n(", la.full_address,")") AS location_arrival'),
            DB::raw('group_concat(vg.name SEPARATOR \' ; \') as vehicle_group_name'),
            DB::raw('group_concat(ocvg.vehicle_number SEPARATOR \' ; \') as vehicle_number'),
        ])->get();
    }
    

    // Lấy dữ liệu đơn hàng khách hàng để xuất theo mẫu
    // CreatedBy nlhoang 28/07/2020
    // ModifiedBy nlhoang 21/08/2020 bổ sung in danh sách hàng hoá của đơn hàng
    public function getDataForTemplateByID($ids, $template)
    {
        $table_name = 'order_customer';
        $ordersQuery = DB::table($table_name . ' as oc')
            ->leftJoin('customer as c', 'c.id', '=', 'oc.customer_id')
            ->leftJoin('locations as l1', 'l1.id', '=', 'oc.location_destination_id')
            ->leftJoin('m_province as lpd', 'l1.province_id', '=', 'lpd.province_id')
            ->leftJoin('m_district as ldd', 'l1.district_id', '=', 'ldd.district_id')
            ->leftJoin('locations as l2', 'l2.id', '=', 'oc.location_arrival_id')
            ->leftJoin('m_province as lpa', 'l2.province_id', '=', 'lpa.province_id')
            ->leftJoin('m_district as lda', 'l2.district_id', '=', 'lda.district_id')
            ->leftJoin('orders as o', 'o.order_customer_id', '=', 'oc.id')
            ->leftJoin('admin_users', 'admin_users.id', '=', 'oc.payment_user_id')
            ->whereIn('oc.id', $ids)
            ->groupBy('oc.id');

        $status = convertCaseWhenQuery(config('system.order_customer_status'), 'oc.status', 'status');

        $items = $ordersQuery->get([
            'oc.*',
            'c.customer_code as code_of_customer_id',
            'c.full_name as name_of_customer_id',
            'l1.title as name_of_location_destination_id',
            'l2.title as name_of_location_arrival_id',
            DB::raw('CASE WHEN oc.payment_type = 1 THEN "Chuyển khoản" ELSE "Tiền mặt" END as payment_type_title'),
            'admin_users.username as pu_username',
            DB::raw('CASE WHEN oc.vat = 1 THEN "Có" ELSE "Không" END as vat_title'),
            DB::raw($status),
            'lpd.title as name_of_province_destination_id',
            'lpa.title as name_of_province_arrival_id',
            'ldd.title as name_of_district_destination_id',
            'lda.title as name_of_district_arrival_id',
        ]);


        // Lấy thông tin xe
        $listOrder = DB::table('order_customer as oc')
            ->leftJoin('orders as o', 'o.order_customer_id', '=', 'oc.id')
            ->leftJoin('vehicle as v', 'v.id', '=', 'o.vehicle_id')
            ->leftJoin('m_vehicle_group as vg', 'vg.id', '=', 'v.group_id')
            ->leftJoin('drivers as d', 'd.id', '=', 'o.primary_driver_id')
            ->whereIn('oc.id',  $ids)
            ->select([
                'oc.id as order_customer_id',
                'o.order_code',
                'v.reg_no',
                'vg.name',
                'vg.name as vehicle_group',
                'd.full_name as driver_name'
            ])
            ->orderBy('o.ins_date')
            ->get();

        // Lấy thông tin hàng hoá
        $listGoods = DB::table('order_customer as oc')
            ->leftJoin('orders as o', 'o.order_customer_id', '=', 'oc.id')
            ->leftJoin('order_goods as og', 'og.order_id', '=', 'o.id')
            ->leftJoin('goods_type as gt', 'og.goods_type_id', '=', 'gt.id')
            ->whereIn('oc.id',  $ids)
            ->orderBy('gt.title');
        if ($template->is_print_empty_cost == 1) {
            $goods_list = DB::table('goods_type as rp')->where('del_flag', '=', 0)->orderBy('title')->get();
        } else {
            $goods_list = $listGoods->select([
                'gt.id',
                'gt.title as name'
            ])->distinct()->get();
        }

        $listGoods = $listGoods->select([
            'oc.id as order_customer_id',
            'og.quantity',
            'gt.id',
            'gt.title as name'
        ])->get();

        $data = [];
        foreach ($items as &$item) {
            $temp_list_order = [];
            foreach ($listOrder as  $order_item) {
                $temp = new \stdClass();
                if ($order_item->order_customer_id == $item->id) {
                    $temp->order_code = $order_item->order_code;
                    $temp->reg_no = $order_item->reg_no;
                    $temp->vehicle_group = $order_item->vehicle_group;
                    $temp->driver_name = $order_item->driver_name;
                    $temp_list_order[] = $temp;
                }
            }
            $item->list_order = $temp_list_order;

            $temp_list_goods = [];
            foreach ($goods_list as  $goods_item) {
                $temp = new \stdClass();
                foreach ($listGoods as $key => $var) {
                    if (($var->order_customer_id === $item->id) &&
                        $var->id === $goods_item->id
                    ) {
                        $temp->quantity = $var->quantity;
                        $temp->name = $var->name;
                    }
                }
                $temp_list_goods[] = $temp;
            }

            $item->list_goods = $temp_list_goods;

            $data[] = $item;
        }
        return $data;
    }
}
