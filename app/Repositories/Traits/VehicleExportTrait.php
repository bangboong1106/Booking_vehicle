<?php

namespace App\Repositories\Traits;

use App\Model\Entities\ReceiptPayment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use stdClass;


trait VehicleExportTrait
{
    // Lấy dữ liệu Xe để xuất theo mẫu
    // CreatedBy nlhoang 10/04/2020
    public function getDataForTemplateByID($ids, $parameter, $template)
    {
        $table_name = "vehicle";
        $query = DB::table($table_name . ' as v')
            ->leftJoin('gps_company as gc', 'v.gps_company_id', '=', 'gc.id')
            ->leftJoin('m_vehicle_group as vg', 'v.group_id', '=', 'vg.id')
            ->leftJoin('vehicle_general_info as vgi', 'v.id', '=', 'vgi.vehicle_id')
            ->where(function ($query) {
                $query->where('vgi.del_flag', '=', '0')
                    ->orWhereNull('vgi.del_flag');
            })
            ->whereIn('v.id', $ids);

        $vehicle_status = convertCaseWhenQuery(config('system.vehicle_status'), 'status', 'vehicle_status');
        $vehicle_type = convertCaseWhenQuery(config('system.vehicle_type'), 'type', 'vehicle_type');
        $active_status = convertCaseWhenQuery(config('system.vehicle_active'), 'active', 'active_status');

        $data = $query->get([
            'v.*',
            'vg.name as group',
            'gc.name as gps_company',
            'vgi.max_fuel',
            'vgi.max_fuel_with_goods',
            'vgi.category_of_barrel',
            'vgi.weight_lifting_system',
            'vgi.register_year',
            'vgi.brand',
            DB::raw($vehicle_status),
            DB::raw($vehicle_type),
            DB::raw($active_status)
        ]);

        $drivers = DB::table('vehicle as v')
            ->leftJoin('driver_vehicle as dv', 'dv.vehicle_id', '=', 'v.id')
            ->leftJoin('drivers as d', 'd.id', '=', 'dv.driver_id')
            ->whereIn('v.id', $ids)
            ->where('dv.del_flag', '=', '0')
            ->orderBy('d.ins_date')
            ->get([
                'v.id',
                'd.full_name as name'
            ]);

        $startDate = $parameter->startDate;
        $endDate = $parameter->endDate;

        // cấu truy vấn lấy ra thông tin theo GPS
        $gpsDataQuery = DB::table('vehicle as v')
            ->leftJoin('vehicle_daily_report as vdr', 'v.reg_no', '=', 'vdr.reg_no')
            ->whereIn('v.id', $ids)
            ->where('v.del_flag', '=', '0')
            ->where('vdr.del_flag', '=', '0')
            ->whereBetween('vdr.date', [$startDate, $endDate])
            ->groupBy('v.id')
            ->select('v.id', DB::raw('SUM(vdr.distance) AS distance_by_gps'));
        $gpsData = $gpsDataQuery->get();

        // Lấy giá dầu theo cấu hình
        $costFuelData = DB::table('system_config as sc')
            ->where('key', '=', 'Cost.Fuel')
            ->where('del_flag', '=', '0')
            ->select('value')->first();
        $costFuel = isset($costFuelData) ? intval($costFuelData->value) : 0;

        //Lấy danh sách cost của xe
        $dayCondition = env('DAY_CONDITION_DEFAULT', 4);

        $receiptPaymentTable = 'm_receipt_payment';
        $costsData = DB::table('routes as r')
            ->leftJoin('route_cost as rc', 'rc.route_id', 'r.id')
            ->leftJoin($receiptPaymentTable . ' as rp', 'rp.id', 'rc.receipt_payment_id')
            ->where([
                ['r.del_flag', '=', '0'],
                ['rc.del_flag', '=', '0'],
                ['r.is_approved', '=', '1'],
                ['rc.amount', '>', '0']
            ])
            ->whereIn('r.vehicle_id', $ids);
        if ($dayCondition == 1) {
            $where = [
                ['r.route_status', '<>', '2'],
                ['r.ETD_date', '>=', $parameter->startDate],
                ['r.ETD_date', '<=', $parameter->endDate]
            ];
        } else if ($dayCondition == 2) {
            $where = [
                ['r.route_status', '<>', '2'],
                ['r.ETD_date_reality', '>=', $parameter->startDate],
                ['r.ETD_date_reality', '<=', $parameter->endDate]
            ];
        } else if ($dayCondition == 3) {
            $where = [
                ['r.route_status', '<>', '2'],
                ['r.ETA_date', '>=', $parameter->startDate],
                ['r.ETA_date', '<=', $parameter->endDate]
            ];
        } else {
            $where = [
                ['r.route_status', '=', '1'],
                ['r.ETA_date_reality', '>=', $parameter->startDate],
                ['r.ETA_date_reality', '<=', $parameter->endDate]
            ];
        }
        $costsData = $costsData->where($where);
        $costsData = $costsData->groupBy('r.vehicle_id', 'rc.receipt_payment_id')
            ->get([
                'r.vehicle_id',
                'rc.receipt_payment_id',
                'rp.name',
                DB::raw('SUM(rc.amount) AS amount')
            ]);

        //Lấy doanh thu ,phí hoa hồng của xe
        $revenueData = DB::table('orders as o')
            ->where([
                ['o.del_flag', '=', '0'],
            ])
            ->whereIn('o.vehicle_id', $ids);
        if ($dayCondition == 1) {
            $revenueData = $revenueData->whereIn('o.status', [2, 3, 4, 5, 7])
                ->where([
                    ['o.ETD_date', '>=', $parameter->startDate],
                    ['o.ETD_date', '<=', $parameter->endDate]
                ]);
        } else if ($dayCondition == 2) {
            $revenueData = $revenueData->whereIn('o.status', [4, 5])
                ->where([
                    ['o.ETD_date', '>=', $parameter->startDate],
                    ['o.ETD_date', '<=', $parameter->endDate]
                ]);
        } else if ($dayCondition == 3) {
            $revenueData = $revenueData->whereIn('o.status', [2, 3, 4, 5, 7])
                ->where([
                    ['o.ETA_date', '>=', $parameter->startDate],
                    ['o.ETA_date', '<=', $parameter->endDate]
                ]);
        } else {
            $revenueData = $revenueData->where([
                ['o.status', '=', '5'],
                ['o.ETA_date_reality', '>=', $parameter->startDate],
                ['o.ETA_date_reality', '<=', $parameter->endDate]
            ]);
        }
        $revenueData = $revenueData->groupBy('o.vehicle_id')
            ->get([
                'o.vehicle_id',
                DB::raw('SUM(o.amount) amount'),
                DB::raw('SUM(o.commission_amount) commission_amount')
            ]);

        $costs = null;
        if ($template->is_print_empty_cost == 1) {
            if (empty($template->list_item)) {
                $costs = DB::table($receiptPaymentTable . ' as rp')->orderBy('rp.sort_order')->get();
            } else {
                $listIDs = explode(",", $template->list_item);
                $costs = DB::table($receiptPaymentTable . ' as rp')->orderBy('name')->whereIn('rp.id', $listIDs)->get();
            }
        } else {
            $cost_query = DB::table('routes as r')
                ->join('route_cost as rc', 'rc.route_id', 'r.id')
                ->join($receiptPaymentTable . ' as rp', 'rp.id', 'rc.receipt_payment_id')
                ->where([
                    ['r.del_flag', '=', '0'],
                    ['rc.del_flag', '=', '0'],
                    ['r.is_approved', '=', '1'],
                    ['rc.amount', '>', '0']
                ])
                ->where($where)
                ->whereIn('r.vehicle_id', $ids)
                ->distinct()
                ->orderBy('rp.sort_order');

            if (empty($template->list_item)) {
                $costs  =   $cost_query->get([
                    'rp.id',
                    'rp.name',
                ]);
            } else {
                $listIDs = explode(",", $template->list_item);
                $costs  =    $cost_query->whereIn('rp.id', $listIDs)->get([
                    'rp.id',
                    'rp.name',
                ]);
            }
        }
        $results = [];
        foreach ($data as $item) {
            $array_filter = [];
            foreach ($drivers as $var) {
                if ($var->id === $item->id) {
                    $array_filter[] = $var;
                }
            }
            foreach ($gpsData as $gps) {
                if ($gps->id === $item->id) {
                    $item->distance_by_gps = $gps->distance_by_gps / 1000;
                    $cost = $costFuel * (isset($gps->distance_by_gps) ? $gps->distance_by_gps / 1000 : 0) * (isset($item->max_fuel) ? $item->max_fuel : 0) / 100;
                    $item->fuel_by_gps = intval($cost);
                }
            }
            $item->drivers = collect($array_filter)->implode('name', '| ');

            $listCosts = [];
            $filteredCost = $costsData->filter(function ($value) use ($item) {
                return $value->vehicle_id == $item->id;
            });
            $totalPayment = 0;
            foreach ($costs as $costVar) {
                $costAmount = 0;
                foreach ($filteredCost as $cost) {
                    if ($costVar->name == $cost->name) {
                        $costAmount = $cost->amount;
                        $totalPayment += $cost->amount;
                    }
                }
                $temp = new \stdClass();
                $temp->payment = $costVar->name;
                $temp->amount = $costAmount;
                $listCosts[] = $temp;
            }

            $filteredRevenue = $revenueData->first(function ($value) use ($item) {
                return $value->vehicle_id == $item->id;
            });

            $totalRevenue = 0;
            $totalCom = 0;
            if ($filteredRevenue) {
                $totalRevenue = $filteredRevenue->amount;
                $totalCom = $filteredRevenue->commission_amount;
            }

            $item->list_costs = $listCosts;
            $item->total_payment = $totalPayment;
            $item->total_revenue = $totalRevenue;
            $item->total_com_amount = $totalCom;
            $item->total_profit = $totalRevenue - $totalPayment;

            $results[] = $item;
        }

        return $results;
    }
}
