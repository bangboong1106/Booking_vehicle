<?php

namespace App\Repositories\Traits;

use App\Model\Entities\ReceiptPayment;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\DB;
use stdClass;


trait RouteExportTrait
{

    //Lấy thông tin vin_no,model_no,location
    public function getExtendPropertyByIDs($ids)
    {
        if (empty($ids)) {
            return [];
        }
        
        $result = DB::table('routes as r')
            ->leftJoin('orders as o', 'o.route_id', '=', 'r.id')
            ->leftJoin('locations as ld', 'o.location_destination_id', '=', 'ld.id')
            ->leftJoin('m_province as lpd', 'ld.province_id', '=', 'lpd.province_id')
            ->leftJoin('m_district as ldd', 'ld.district_id', '=', 'ldd.district_id')
            ->leftJoin('locations as la', 'o.location_arrival_id', '=', 'la.id')
            ->leftJoin('m_province as lpa', 'la.province_id', '=', 'lpa.province_id')
            ->leftJoin('m_district as lda', 'la.district_id', '=', 'lda.district_id')
            ->where([
                ['r.del_flag', '=', 0],
            ])
            ->whereIn('r.id', $ids)
            ->groupBy('r.id')
            ->get([
                'r.id',
                DB::raw('group_concat(o.vin_no SEPARATOR \' \r\n\') as vin_no'),
                DB::raw('group_concat(o.model_no SEPARATOR \' \r\n\') as model_no'),
                DB::raw('group_concat(ld.title ORDER BY o.id SEPARATOR \' \r\n -\') as location_destination'),
                DB::raw('group_concat(la.title ORDER BY o.id SEPARATOR \' \r\n -\') as location_arrival'),

                DB::raw('group_concat(ldd.title ORDER BY o.id SEPARATOR \' \r\n -\') as district_destination'),
                DB::raw('group_concat(lda.title ORDER BY o.id SEPARATOR \' \r\n -\') as district_arrival'),

                DB::raw('group_concat(lpd.title ORDER BY o.id SEPARATOR \' \r\n -\') as province_destination'),
                DB::raw('group_concat(lpa.title ORDER BY o.id SEPARATOR \' \r\n -\') as province_arrival'),

                DB::raw('group_concat(o.order_code ORDER BY o.id SEPARATOR \' \r\n\') as list_orders'),
                DB::raw('group_concat(o.weight ORDER BY o.id SEPARATOR \' \r\n\') as list_weight'),
                DB::raw('group_concat(o.volume ORDER BY o.id SEPARATOR \' \r\n\') as list_volume'),
                DB::raw('group_concat(o.quantity ORDER BY o.id SEPARATOR \' \r\n\') as list_quantity'),

                DB::raw('group_concat(DATE_FORMAT(o.ETD_date,"%d-%m-%Y") ORDER BY o.id SEPARATOR \' \r\n\') as ETD_date'),
                DB::raw('group_concat(DATE_FORMAT(o.ETD_date,"%d-%m-%Y") ORDER BY o.id SEPARATOR \' \r\n\') as ETA_date'),

                DB::raw('group_concat(o.note ORDER BY o.id SEPARATOR \' \r\n -\') as list_order_note'),
            ]);

        return $result;
    }

    // Lấy dữ liệu Chuyến xe để xuất theo mẫu
    // CreatedBy nlhoang 10/04/2020
    public function getDataForTemplateByID($ids, $template)
    {
        $table_name = $this->getTableName();
        $query = \DB::table($table_name . ' as r')
            ->leftJoin('quota as q', 'r.quota_id', '=', 'q.id')
            ->leftJoin('vehicle as v', 'r.vehicle_id', '=', 'v.id')
            ->leftJoin('vehicle_general_info as vgi', 'v.id', '=', 'vgi.vehicle_id')
            ->leftJoin('m_vehicle_group as vg', 'v.group_id', '=', 'vg.id')
            ->leftJoin('drivers as d', 'r.driver_id', '=', 'd.id')
            ->leftJoin('admin_users as a', 'r.approved_id', '=', 'a.id')
            ->leftJoin('admin_users as ai', 'r.ins_id', '=', 'ai.id')
            ->whereIn('r.id', $ids)
            ->orderBy('r.ins_date', 'DESC');
        $status = convertCaseWhenQuery(config('system.route_status'), 'r.route_status', 'routes_status');
        $approve_status = convertCaseWhenQuery(config('system.route_is_approved'), 'r.is_approved', 'approve_status');

        $data = $query->get([
            'r.*',
            'q.name as quota',
            'v.reg_no as vehicle',
            'd.full_name as driver',
            'a.full_name as approved_name',
            'ai.full_name as ins_user',
            'vg.name as vehicle_group',
            'vgi.max_fuel as max_fuel',
            'vgi.max_fuel_with_goods as max_fuel_with_goods',
            DB::raw($status),
            DB::raw($approve_status)
        ]);

        $orders = \DB::table($table_name . ' as q')
            ->leftJoin('orders as o', 'o.route_id', '=', 'q.id')
            ->leftJoin('customer as c', 'o.customer_id', '=', 'c.id')
            ->whereIn('q.id', $ids)
            ->orderBy('o.ins_date')
            ->get([
                'o.route_id',
                'o.order_code',
                DB::raw('COALESCE(c.full_name, "-") as customer_full_name'),
                DB::raw('COALESCE(o.quantity, 0) as quantity'),
                DB::raw('COALESCE(o.weight, 0) as weight'),
                DB::raw('COALESCE(o.volume, 0) as volume'),
                DB::raw('COALESCE(o.commission_amount, 0) as commission_amount'),
                
            ]);

        $list_locations = \DB::table($table_name . ' as q')
            ->leftJoin('orders as o', 'o.route_id', '=', 'q.id')
            ->leftJoin('locations as l1', 'l1.id', '=', 'o.location_destination_id')
            ->leftJoin('m_province as lpd', 'l1.province_id', '=', 'lpd.province_id')
            ->leftJoin('m_district as ldd', 'l1.district_id', '=', 'ldd.district_id')
            ->leftJoin('locations as l2', 'l2.id', '=', 'o.location_arrival_id')
            ->leftJoin('m_province as lpa', 'l2.province_id', '=', 'lpa.province_id')
            ->leftJoin('m_district as lda', 'l2.district_id', '=', 'lda.district_id')
            ->whereIn('q.id', $ids)
            ->where('o.del_flag', '=', 0)
            ->orderBy('o.ins_date')
            ->get([
                'o.route_id',
                'o.location_destination_id as destination_location_id',
                'o.location_arrival_id as arrival_location_id',
                'o.ETD_date as destination_location_date',
                'o.ETD_time as destination_location_time',
                'o.ETA_date as arrival_location_date',
                'o.ETA_time as arrival_location_time',
                'l1.title as destination_location_title',
                'l2.title as arrival_location_title',
                'lpd.title as name_of_province_destination_id',
                'lpa.title as name_of_province_arrival_id',
                'ldd.title as name_of_district_destination_id',
                'lda.title as name_of_district_arrival_id',
            ]);

        $costs = null;

        $receiptPaymentTable = ReceiptPayment::getTableName();
        if ($template->is_print_empty_cost == 1) {
            if (empty($template->list_item)) {
                $costs = DB::table($receiptPaymentTable . ' as rp')->orderBy('name')->get();
            } else {
                $listIDs = explode(",", $template->list_item);
                $costs = DB::table($receiptPaymentTable . ' as rp')->orderBy('name')->whereIn('rp.id', $listIDs)->get();
            }
        } else {

            $costs_query = \DB::table($receiptPaymentTable . ' as rp')
                ->join('route_cost as dv', 'dv.receipt_payment_id', '=', 'rp.id')
                ->whereIn('dv.route_id', $ids)
                ->distinct()
                ->orderBy('rp.sort_order');

            if (empty($template->list_item)) {
                $costs = $costs_query->get([
                    'rp.id',
                    'rp.name',
                ]);
            } else {
                $listIDs = explode(",", $template->list_item);
                $costs = $costs_query->whereIn('rp.id', $listIDs)->get([
                    'rp.id',
                    'rp.name',
                ]);
            }
        }

        $list_costs = \DB::table($receiptPaymentTable . ' as rp')
            ->leftJoin('route_cost as dv', 'dv.receipt_payment_id', '=', 'rp.id')
            ->leftJoin($table_name . ' as q', 'dv.route_id', '=', 'q.id')
            ->whereIn('q.id', $ids)
            ->get([
                'dv.*',
                'rp.name as receipt_payment'
            ]);
        $results = [];
        foreach ($data as $item) {
            $array_filter = [];
            foreach ($orders as $key => $var) {
                if (($var->{'route_id'} === $item->{'id'})) {
                    $array_filter[] = $var;
                }
            }
            $item->vin_nos = str_replace(";", "\n", $item->vin_nos);
            $item->model_nos = str_replace(";", "\n", $item->model_nos);
            $item->orders = $item->order_codes;
            $item->routes_amount = $item->total_amount;
            $item->total_quantity =  $item->quantity;
            $item->total_volume = $item->volume;
            $item->total_weight = $item->weight;
            $item->total_order = $item->count_order;
            $item->list_order =  collect($array_filter)->implode('order_code', PHP_EOL);
            $item->list_customer =  collect($array_filter)->implode('customer_full_name', PHP_EOL);
            $item->list_quantity =  collect($array_filter)->pluck('quantity')->map(function ($name) {
                return ($name);
            })->implode(PHP_EOL);
            $item->list_weight =  collect($array_filter)->pluck('weight')->map(function ($name) {
                return ($name);
            })->implode(PHP_EOL);
            $item->list_volume =  collect($array_filter)->pluck('volume')->map(function ($name) {
                return ($name);
            })->implode(PHP_EOL);

            $item->list_commission_amount =  collect($array_filter)->pluck('commission_amount')->map(function ($name) {
                return ($name);
            })->implode(PHP_EOL);

            $array_filter1 = [];
            foreach ($list_locations as $key => $var) {
                if (($var->{'route_id'} === $item->{'id'})) {
                    $array_filter1[] = $var;
                }
            }
            $item->list_locations = $array_filter1;

            $temp_costs = [];
            foreach ($costs as $costVar) {
                $temp = new \stdClass();
                foreach ($list_costs as $var) {
                    if (($var->{'route_id'} === $item->{'id'}) &&
                        $var->{'receipt_payment_id'} === $costVar->{'id'}
                    ) {
                        $temp->{'amount'} = $var->{'amount'};
                        $temp->{'amount_admin'} = $var->{'amount_admin'};
                        $temp->{'amount_driver'} = $var->{'amount_driver'};
                    }
                }
                $temp->{'receipt_payment'} = $costVar->{'name'};
                $temp_costs[] = $temp;
            }
            $item->total_amount = collect($temp_costs)->sum('amount');
            $item->total_amount_admin = collect($temp_costs)->sum('amount_admin');
            $item->total_amount_driver = collect($temp_costs)->sum('amount_driver');

            $item->list_costs = $temp_costs;

            $results[] = $item;
        }
        return $results;
    }
}
