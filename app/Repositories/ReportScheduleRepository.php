<?php

namespace App\Repositories;

use App\Common\AppConstant;
use App\Model\Entities\ReportSchedule;
use App\Repositories\Base\CustomRepository;
use DB;
use Illuminate\Support\Facades\Auth;

class ReportScheduleRepository extends CustomRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    function model()
    {
        return ReportSchedule::class;
    }

    public function validator()
    {
        return \App\Validators\ReportScheduleValidator::class;
    }

    public function getReportScheduleWithID($id)
    {
        if ($id)
            return $this->search([
                'id_eq' => $id
            ])->first();
        return null;
    }

    public function getReportScheduleByTime()
    {
        $query = "SELECT *
                    FROM report_schedules
                    WHERE
                      del_flag = '0'
                     AND (time_to_send = concat(DATE_FORMAT(NOW(), '%k:%i'), ':00'))
                     AND (date_from IS NULL OR date(date_from) <= CURDATE())
                     AND (date_to IS NULL OR date(date_to) >= CURDATE())";
        return DB::select($query);
    }

    public function reportSchedule($staticBy, $from_date, $to_date)
    {
        $procedureName = "";
        switch ($staticBy) {
            case "day":
                $procedureName = 'proc_report_vehicle_by_turn';
                break;
            case "month":
                $procedureName = 'proc_report_vehicle_by_turn_monthly';
                break;
        }
        $sql = 'call ' . $procedureName . '(?,?,?,?,?,?,?,?,?,?,?,?)';

        $query = DB::select($sql, array(0, null, null, null, null, 1, 1, 1, 1, $from_date, $to_date));

        $summaryQuery = DB::select($sql, array(1, null, null, null, null, 1, 1, 1, 1, $from_date, $to_date));

        $results = ["data" => $query, "summary" => $summaryQuery];
        return $results;
    }

    public function reportVehiclePerformance($vehicleTeamIDs, $vehicleIDs, $dayCondition, $startDate, $endDate, $partnerIds = [])
    {
        try {
            $user = Auth::user();
            $partnerId = $partnerIds;

            if ($user->role == 'partner' && $user->partner_id != "") {
                $partnerId = [];
                $partnerId[] = $user->partner_id;
            }

            $dateDiff = intval(abs(strtotime($startDate) - strtotime($endDate)) / 86400);

            // cấu truy vấn lấy ra thông tin theo GPS
            $gpsData = DB::table('vehicle as v')
                ->leftJoin('vehicle_daily_report as vdr', 'v.reg_no', '=', 'vdr.reg_no')
                ->where('v.del_flag', '=', '0')
                ->where('vdr.del_flag', '=', '0')
                ->whereBetween('vdr.date', [$startDate, $endDate])
                ->groupBy('v.id')
                ->get(['v.id', DB::raw('SUM(vdr.distance) AS distance_by_gps')]);

            //Lấy tổng số chuyến, chi phí của xe
            $dayCondition = !empty($dayCondition) ? $dayCondition : env('DAY_CONDITION_DEFAULT', 4);

            $routeData = DB::table('routes as r')
                ->where([
                    ['r.del_flag', '=', '0']
                ]);
            if ($dayCondition == 1) {
                $routeData = $routeData->where([
                    ['r.route_status', '<>', '2'],
                    ['r.ETD_date', '>=', $startDate],
                    ['r.ETD_date', '<=', $endDate]
                ]);
            } else if ($dayCondition == 2) {
                $routeData = $routeData->whereNotNull('r.ETD_date_reality')
                    ->where([
                        ['r.ETD_date_reality', '>=', $startDate],
                        ['r.ETD_date_reality', '<=', $endDate]
                    ]);
            } else if ($dayCondition == 3) {
                $routeData = $routeData->where([
                    ['r.route_status', '<>', '2'],
                    ['r.ETA_date', '>=', $startDate],
                    ['r.ETA_date', '<=', $endDate]
                ]);
            } else {
                $routeData = $routeData->where([
                    ['r.route_status', '=', '1'],
                    ['r.ETA_date_reality', '>=', $startDate],
                    ['r.ETA_date_reality', '<=', $endDate]
                ]);
            }
            $routeData = $routeData->groupBy('r.vehicle_id')
                ->get([
                    'r.vehicle_id', DB::raw('SUM(CASE WHEN r.is_approved = 1 THEN r.final_cost ELSE 0 END) AS cost_amount'),
                    DB::raw('COUNT(r.id) AS total_route')
                ]);

            //Lấy tổng số đơn ,doanh thu ,phí hoa hồng, cod của xe
            $orderData = DB::table('orders as o')
                ->where([
                    ['o.del_flag', '=', '0'],
                ]);
            if ($dayCondition == 1) {
                $orderData = $orderData->whereIn('o.status', [2, 3, 4, 5, 7])
                    ->where([
                        ['o.ETD_date', '>=', $startDate],
                        ['o.ETD_date', '<=', $endDate]
                    ]);
            } else if ($dayCondition == 2) {
                $orderData = $orderData->whereIn('o.status', [4, 5])
                    ->where([
                        ['o.ETD_date_reality', '>=', $startDate],
                        ['o.ETD_date_reality', '<=', $endDate]
                    ]);
            } else if ($dayCondition == 3) {
                $orderData = $orderData->whereIn('o.status', [2, 3, 4, 5, 7])
                    ->where([
                        ['o.ETA_date', '>=', $startDate],
                        ['o.ETA_date', '<=', $endDate]
                    ]);
            } else {
                $orderData = $orderData->where([
                    ['o.status', '=', '5'],
                    ['o.ETA_date_reality', '>=', $startDate],
                    ['o.ETA_date_reality', '<=', $endDate]
                ]);
            }
            $orderData = $orderData->groupBy('o.vehicle_id')
                ->get([
                    'o.vehicle_id', DB::raw('SUM(o.amount) amount'),
                    DB::raw('SUM(o.commission_amount) commission_amount'),
                    DB::raw('SUM(o.cod_amount) cod_amount'),
                    DB::raw('COUNT(o.id) AS total_order'),
                    DB::raw('SUM(CASE WHEN o.status = 5 AND DATE_FORMAT(CONCAT(o.ETA_date_reality,\' \',o.ETA_time_reality) ,\'%Y-%m-%d %H:%i\') 
                                <= DATE_FORMAT(CONCAT(o.ETA_date,\' \',o.ETA_time) ,\'%Y-%m-%d %H:%i\') THEN 1 ELSE 0 END) AS total_order_on_time'),
                    DB::raw('SUM(CASE WHEN o.status = 5 AND DATE_FORMAT(CONCAT(o.ETA_date_reality,\' \',o.ETA_time_reality),\'%Y-%m-%d %H:%i\') 
                                > DATE_FORMAT(CONCAT(o.ETA_date,\' \',o.ETA_time) ,\'%Y-%m-%d %H:%i\') THEN 1 ELSE 0 END) AS total_order_late')
                ]);

            $vehicles = DB::table('vehicle as v')
                ->leftJoin('driver_vehicle as dv', 'dv.vehicle_id', '=', 'v.id')
                ->leftJoin('drivers as d', 'd.id', '=', 'dv.driver_id')
                ->leftJoin('driver_vehicle_team as dvt', 'dvt.driver_id', '=', 'd.id')
                ->where('dv.del_flag', '=', '0')
                ->where('v.del_flag', '=', '0')
                ->where('d.del_flag', '=', '0');
            if (!empty($vehicleIDs)) {
                $vehicles = $vehicles->whereIN('v.id', $vehicleIDs);
            }
            if (!empty($vehicleTeamIDs)) {
                $vehicles = $vehicles->whereIN('dvt.vehicle_team_id', $vehicleTeamIDs);
            }

            if (!empty($partnerId)) {
                $vehicles = $vehicles->whereIN('v.partner_id', $partnerId);
            }

            $vehicles = $vehicles->groupBy('v.id')
                ->orderBy('v.reg_no', 'ASC')
                ->get([
                    'v.id',
                    'v.reg_no',
                    DB::raw('group_concat(distinct(d.full_name) SEPARATOR \' , \') as driver_names')
                ]);

            $results = [];
            foreach ($vehicles as $item) {
                $distance = 0;
                $amount = 0;
                $costAmount = 0;
                $commissionAmount = 0;
                $codAmount = 0;
                $totalRoute = 0;
                $totalOrder = 0;
                $totalOrderOnTime = 0;
                $totalOrderLate = 0;

                if ($gpsData) {
                    $gpsItem = $gpsData->first(function ($value) use ($item) {
                        return $value->id == $item->id;
                    });
                    if ($gpsItem)
                        $distance = $gpsItem->distance_by_gps;
                }

                if ($orderData) {
                    $orderItem = $orderData->first(function ($value) use ($item) {
                        return $value->vehicle_id == $item->id;
                    });
                    if ($orderItem) {
                        $amount = $orderItem->amount;
                        $commissionAmount = $orderItem->commission_amount;
                        $codAmount = $orderItem->cod_amount;
                        $totalOrder = $orderItem->total_order;
                        $totalOrderOnTime = $orderItem->total_order_on_time;
                        $totalOrderLate = $orderItem->total_order_late;
                    }
                }

                if ($routeData) {
                    $routeItem = $routeData->first(function ($value) use ($item) {
                        return $value->vehicle_id == $item->id;
                    });
                    if ($routeItem) {
                        $costAmount = $routeItem->cost_amount;
                        $totalRoute = $routeItem->total_route;
                    }
                }

                $results[] = [
                    'reg_no' => $item->reg_no,
                    'driver_names' => $item->driver_names,
                    'distance' => $distance,
                    'distance_average_per_day' => round($dateDiff != 0 ? ($distance / $dateDiff) : 0, 0),
                    'total_order' => $totalOrder,
                    'total_order_on_time' => $totalOrderOnTime,
                    'total_order_late' => $totalOrderLate,
                    'ratio_order' => round($totalOrder != 0 && ($totalOrderOnTime + $totalOrderLate) !=0 ? ($totalOrderOnTime / ($totalOrderOnTime + $totalOrderLate)) : 0, 2),
                    'total_route' => $totalRoute,
                    'total_route_average_per_day' => round($dateDiff != 0 ? ($totalRoute / $dateDiff) : 0, 0),
                    'total_amount' => $amount,
                    'total_cost' => $costAmount,
                    'total_commission' => $commissionAmount,
                    'total_cod' => $codAmount,
                    'revenue' => $amount - $costAmount,
                    'ratio_revenue' => round($amount != 0 ? (($amount - $costAmount) / $amount) : 0, 2)
                ];
            }

            return $results;
        } catch (\Exception $exception) {
            logError($exception);
        }
    }

    //Báo cáo doanh thu chi phi theo khách hàng
    public function reportCustomer($dayCondition, $startDate, $endDate, $customerIds, $customerGroupIds)
    {
        try {
            //Lấy chi phí của khách hàng
            $subquery = "(SELECT DISTINCT r.id as route_id, o.customer_id 
                          FROM routes r
                          INNER JOIN orders o ON o.route_id = r.id
                          WHERE r.del_flag = 0 
                          AND o.del_flag = 0) as tmp";
            $routeData = DB::table('routes as r')
                ->leftJoin(DB::raw($subquery), 'tmp.route_id', 'r.id');
            if (!empty($customerGroupIds)) {
                $routeData = $routeData->leftJoin('customer_group_customer as cgc', 'cgc.customer_id', 'tmp.customer_id');
            }
            $routeData = $routeData->where([
                ['r.del_flag', '=', '0'],
                ['r.is_approved', '=', '1']
            ]);

            if ($dayCondition == 1) {
                $routeData = $routeData->where([
                    ['r.route_status', '<>', '2'],
                    ['r.ETD_date', '>=', $startDate],
                    ['r.ETD_date', '<=', $endDate]
                ]);
            } else if ($dayCondition == 2) {
                $routeData = $routeData->whereNotNull('r.ETD_date_reality')
                    ->where([
                        ['r.route_status', '<>', '2'],
                        ['r.ETD_date_reality', '>=', $startDate],
                        ['r.ETD_date_reality', '<=', $endDate]
                    ]);
            } else if ($dayCondition == 3) {
                $routeData = $routeData->where([
                    ['r.route_status', '<>', '2'],
                    ['r.ETA_date', '>=', $startDate],
                    ['r.ETA_date', '<=', $endDate]
                ]);
            } else {
                $routeData = $routeData->where([
                    ['r.route_status', '=', '1'],
                    ['r.ETA_date_reality', '>=', $startDate],
                    ['r.ETA_date_reality', '<=', $endDate]
                ]);
            }

            if (!empty($customerIds)) {
                $routeData = $routeData->whereIN('tmp.customer_id', $customerIds);
            }

            if (!empty($customerGroupIds)) {
                $routeData = $routeData->where('cgc.del_flag', '=', '0')
                    ->whereIN('cgc.customer_group_id', $customerGroupIds);
            }

            $routeData = $routeData->groupBy('tmp.customer_id')
                ->get(['tmp.customer_id', DB::raw('SUM(r.final_cost) AS cost_amount')]);

            //Lấy tổng số đơn ,doanh thu ,phí hoa hồng, cod của khách hàng
            $orderData = DB::table('orders as o');
            if (!empty($customerGroupIds)) {
                $orderData = $orderData->leftJoin('customer_group_customer as cgc', 'cgc.customer_id', 'o.customer_id');
            }
            $orderData = $orderData->where([
                ['o.del_flag', '=', '0']
            ]);
            if ($dayCondition == 1) {
                $orderData = $orderData->whereIn('o.status', [2, 3, 4, 5, 7])
                    ->where([
                        ['o.ETD_date', '>=', $startDate],
                        ['o.ETD_date', '<=', $endDate]
                    ]);
            } else if ($dayCondition == 2) {
                $orderData = $orderData->whereIn('o.status', [4, 5])
                    ->where([
                        ['o.ETD_date_reality', '>=', $startDate],
                        ['o.ETD_date_reality', '<=', $endDate]
                    ]);
            } else if ($dayCondition == 3) {
                $orderData = $orderData->whereIn('o.status', [2, 3, 4, 5, 7])
                    ->where([
                        ['o.ETA_date', '>=', $startDate],
                        ['o.ETA_date', '<=', $endDate]
                    ]);
            } else {
                $orderData = $orderData->where([
                    ['o.status', '=', '5'],
                    ['o.ETA_date_reality', '>=', $startDate],
                    ['o.ETA_date_reality', '<=', $endDate]
                ]);
            }

            if (!empty($customerIds)) {
                $orderData = $orderData->whereIN('o.customer_id', $customerIds);
            }

            if (!empty($customerGroupIds)) {
                $orderData = $orderData->where('cgc.del_flag', '=', '0')
                    ->whereIN('cgc.customer_group_id', $customerGroupIds);
            }

            $orderData = $orderData->groupBy('o.customer_id')
                ->get([
                    'o.customer_id', DB::raw('SUM(o.amount) amount'),
                    DB::raw('SUM(o.commission_amount) commission_amount'),
                    DB::raw('SUM(o.cod_amount) cod_amount'),
                    DB::raw('COUNT(o.id) AS total_order')
                ]);

            $customers = DB::table('customer as c');
            if (!empty($customerGroupIds)) {
                $customers = $customers->leftJoin('customer_group_customer as cgc', 'cgc.customer_id', 'c.id');
            }
            $customers = $customers->where('c.del_flag', '=', '0');
            if (!empty($customerIds)) {
                $customers = $customers->whereIN('c.id', $customerIds);
            }

            if (!empty($customerGroupIds)) {
                $customers = $customers->where('cgc.del_flag', '=', '0')
                    ->whereIN('cgc.customer_group_id', $customerGroupIds);
            }
            $customers = $customers->groupBy('c.id')
                ->get([
                    'c.id',
                    'c.full_name'
                ]);

            $results = [];
            $summaryOrder = 0;
            $summaryRevenue = 0;
            $summaryCost = 0;
            $summaryProfit = 0;
            foreach ($customers as $item) {
                $amount = 0;
                $costAmount = 0;
                $commissionAmount = 0;
                $codAmount = 0;
                $totalOrder = 0;

                if ($orderData) {
                    $orderItem = $orderData->first(function ($value) use ($item) {
                        return $value->customer_id == $item->id;
                    });
                    if ($orderItem) {
                        $amount = $orderItem->amount;
                        $commissionAmount = $orderItem->commission_amount;
                        $codAmount = $orderItem->cod_amount;
                        $totalOrder = $orderItem->total_order;
                    }
                }

                if ($routeData) {
                    $routeItem = $routeData->first(function ($value) use ($item) {
                        return $value->customer_id == $item->id;
                    });
                    if ($routeItem) {
                        $costAmount = $routeItem->cost_amount;
                    }
                }

                $results[] = [
                    'customer_name' => $item->full_name,
                    'order_number' => $totalOrder,
                    'revenue' => $amount,
                    'cost' => $costAmount,
                    'commission' => $commissionAmount,
                    'cod' => $codAmount,
                    'profit' => $amount - $costAmount,
                ];
                $summaryOrder += $totalOrder;
                $summaryRevenue += $amount;
                $summaryCost += $costAmount;
                $summaryProfit += ($amount - $costAmount);
            }

            usort($results, function ($first, $second) {
                return $first['profit'] < $second['profit'];
            });

            $results[] = [
                'order_number' => $summaryOrder,
                'revenue' => $summaryRevenue,
                'cost' => $summaryCost,
                'profit' => $summaryProfit
            ];

            return $results;
        } catch (\Exception $exception) {
            logError($exception);
        }
    }

    //Báo cáo hoạt động đội xe
    public function reportVehicleTeam($dayCondition, $startDate, $endDate, $driverIds = null, $vehicleTeamIds = null, $customerIds = null, $partnerIds = null)
    {
        try {
            $user = Auth::user();
            $partnerId = $partnerIds;

            if ($user->role == 'partner' && $user->partner_id != "") {
                $partnerId = [];
                $partnerId[] = $user->partner_id;
            }

            //Lấy tương tác của tài xế
            $sql = "SELECT tmp.id as driver_id, 
                        COUNT(tmp.order_id) total_order_interactive 
                        FROM (
                        SELECT DISTINCT d.id, oh.order_id
                            FROM order_history oh
                            INNER JOIN admin_users au ON oh.ins_id = au.id
                            INNER JOIN drivers d ON au.id = d.user_id
                            INNER JOIN orders o ON o.id = oh.order_id
                            WHERE oh.del_flag = 0 AND au.del_flag = 0  
                            AND d.del_flag = 0 
                            AND au.role = 'driver'
                            AND o.del_flag = 0 
                            AND o.primary_driver_id IS NOT NULL 
                            AND o.primary_driver_id != 0";

            if ($dayCondition == 1) {
                $sql .= " AND o.status IN (2,3,4,5,7) AND o.ETD_date >= " . $startDate
                    . " AND o.ETD_date <= " . $endDate;
            } else if ($dayCondition == 2) {
                $sql .= " AND o.status IN (4,5) AND o.ETD_date_reality >= " . $startDate
                    . " AND o.ETD_date_reality <= " . $endDate;
            } else if ($dayCondition == 3) {
                $sql .= " AND o.status IN (2,3,4,5,7) AND o.ETA_date >= " . $startDate
                    . " AND o.ETA_date <= " . $endDate;
            } else {
                $sql .= " AND o.status = 5 AND o.ETA_date_reality >= " . $startDate
                    . " AND o.ETA_date_reality <= " . $endDate;
            }

            if (!empty($customerIds)) {
                $sql .= " AND o.customer_id IN (" . implode(',', $customerIds) . ")";
            }

            $sql .= " ) tmp";

            if (!empty($driverIds)) {
                $sql .= " WHERE tmp.id IN (" . implode(',', $driverIds) . ")";
            }

            $sql .= " GROUP BY tmp.id";

            $activityData = DB::select(DB::raw($sql));


            //Lấy tổng số đơn ,đơn hoàn thành ,đơn hoàn thành đúng giờ của tài xế
            $orderData = DB::table('orders as o')
                ->where([
                    ['o.del_flag', '=', '0'],
                ]);
            if ($dayCondition == 1) {
                $orderData = $orderData->whereIn('o.status', [2, 3, 4, 5, 7])
                    ->where([
                        ['o.ETD_date', '>=', $startDate],
                        ['o.ETD_date', '<=', $endDate]
                    ]);
            } else if ($dayCondition == 2) {
                $orderData = $orderData->whereIn('o.status', [4, 5])
                    ->where([
                        ['o.ETD_date', '>=', $startDate],
                        ['o.ETD_date', '<=', $endDate]
                    ]);
            } else if ($dayCondition == 3) {
                $orderData = $orderData->whereIn('o.status', [2, 3, 4, 5, 7])
                    ->where([
                        ['o.ETA_date', '>=', $startDate],
                        ['o.ETA_date', '<=', $endDate]
                    ]);
            } else {
                $orderData = $orderData->where([
                    ['o.status', '=', '5'],
                    ['o.ETA_date_reality', '>=', $startDate],
                    ['o.ETA_date_reality', '<=', $endDate]
                ]);
            }

            if (!empty($driverIds)) {
                $orderData = $orderData->whereIN('o.primary_driver_id', $driverIds);
            }
            if (!empty($customerIds)) {
                $orderData = $orderData->whereIN('o.customer_id', $customerIds);
            }

            $orderData = $orderData->groupBy('o.primary_driver_id')
                ->get([
                    'o.primary_driver_id as driver_id',
                    DB::raw('COUNT(o.id) AS total_order'),
                    DB::raw('SUM(CASE WHEN o.status = 5 THEN 1 ELSE 0 END) AS total_order_complete'),
                    DB::raw('SUM(CASE WHEN o.status = 5 AND DATE_FORMAT(CONCAT(o.ETA_date_reality,\' \',o.ETA_time_reality) ,\'%Y-%m-%d %H:%i\') 
                                <= DATE_FORMAT(CONCAT(o.ETA_date,\' \',o.ETA_time) ,\'%Y-%m-%d %H:%i\') THEN 1 ELSE 0 END) AS total_order_on_time'),
                ]);

            //Lay danh sach doi xe
            $vehicleTeamList = DB::table('vehicle_team as vt')
                ->join('driver_vehicle_team as dvt', 'dvt.vehicle_team_id', 'vt.id')
                ->join('drivers as d', 'dvt.driver_id', 'd.id')
                ->where([
                    ['vt.del_flag', '=', '0'],
                    ['d.del_flag', '=', '0']
                ]);
            if (!empty($vehicleTeamIds)) {
                $vehicleTeamList = $vehicleTeamList->whereIN('vt.id', $vehicleTeamIds);
            }
            if (!empty($driverIds)) {
                $vehicleTeamList = $vehicleTeamList->whereIN('d.id', $driverIds);
            }
            if (!empty($partnerId)) {
                $vehicleTeamList = $vehicleTeamList->whereIN('d.partner_id', $partnerId);
            }

            $vehicleTeamList = $vehicleTeamList->groupBy('vt.id', 'd.id')
                ->orderBy('vt.name', 'desc')
                ->get([
                    'd.id as driver_id',
                    'd.full_name as driver_name',
                    'vt.id as vehicle_team_id',
                    'vt.name as vehicle_team_name'
                ]);

            $results = [];
            foreach ($vehicleTeamList as $item) {
                $totalOrder = 0;
                $totalOrderComplete = 0;
                $totalOrderOnTime = 0;
                $totalOrderInteractive = 0;

                if ($orderData) {
                    $orderItem = $orderData->first(function ($value) use ($item) {
                        return $value->driver_id == $item->driver_id;
                    });
                    if ($orderItem) {
                        $totalOrder = $orderItem->total_order;
                        $totalOrderComplete = $orderItem->total_order_complete;
                        $totalOrderOnTime = $orderItem->total_order_on_time;
                    }
                }

                if ($activityData) {
                    $activityItems = array_filter($activityData, function ($value) use ($item) {
                        return $value->driver_id == $item->driver_id;
                    });
                    if ($activityItems && count($activityItems) > 0) {
                        foreach ($activityItems as $activityItem)
                            $totalOrderInteractive = $activityItem->total_order_interactive;
                    }
                }

                $data = [
                    'driver_id' => $item->driver_id,
                    'driver_name' => $item->driver_name,
                    'total_order' => $totalOrder,
                    'total_order_complete' => $totalOrderComplete,
                    'ratio_order_complete' => round($totalOrder == 0 ? 0 : ($totalOrderComplete / $totalOrder) * 100, 2),
                    'total_order_on_time' => $totalOrderOnTime,
                    'ratio_order_on_time' => round($totalOrderComplete == 0 ? 0 : ($totalOrderOnTime / $totalOrderComplete) * 100, 2),
                    'total_order_interactive' => $totalOrderInteractive,
                    'ratio_order_interactive' => round($totalOrder == 0 ? 0 : ($totalOrderInteractive / $totalOrder) * 100, 2)
                ];

                $resultItem = null;
                foreach ($results as &$result) {
                    if ($result['vehicle_team_id'] == $item->vehicle_team_id) {
                        $resultItem = $result;
                        $result['drivers'][] = $data;
                        $result['summary_order'] = $result['summary_order'] + $totalOrder;
                        $result['summary_order_complete'] = $result['summary_order_complete'] + $totalOrderComplete;
                        $result['summary_order_on_time'] = $result['summary_order_on_time'] + $totalOrderOnTime;
                        $result['summary_order_interactive'] = $result['summary_order_interactive'] + $totalOrderInteractive;
                    }
                }
                if ($resultItem == null) {
                    $drivers[] = $data;
                    $results[] = [
                        'vehicle_team_id' => $item->vehicle_team_id,
                        'vehicle_team_name' => $item->vehicle_team_name,
                        'drivers' => $drivers,
                        'summary_order' => $totalOrder,
                        'summary_order_complete' => $totalOrderComplete,
                        'summary_order_on_time' => $totalOrderOnTime,
                        'summary_order_interactive' => $totalOrderInteractive
                    ];
                    unset($drivers);
                }
            }

            return $results;
        } catch (\Exception $exception) {
            logError($exception);
        }
    }

    protected function getQueryBuilder($columns)
    {
        $user = Auth::user();
        $query = $this->getBuilder()->select($this->_buildColumn($columns));

        if ($user->role == 'partner') {
            $query = $query->leftJoin('admin_users', $this->getTableName() . '.ins_id', '=', 'admin_users.id')
                    ->where('admin_users.partner_id', $user->partner_id);
        }
            
        return $query->orderBy($this->getSortField(), $this->getSortType());
    }

    public function reportReportInteractiveDriver($from_date, $to_date)
    {
        $from_date = AppConstant::convertDate($from_date, 'Y-m-d');
        $to_date = AppConstant::convertDate($to_date, 'Y-m-d');
        $sql = "SELECT au.username, d.full_name,vt.name vehicle_team_name, tmp1.tong_don,tmp2.xac_nhan,tmp2.nhan_hang ,tmp2.tra_hang
                FROM drivers as d
                INNER JOIN admin_users au ON au.id = d.user_id
                LEFT JOIN driver_vehicle_team dvt ON dvt.driver_id = d.id
                LEFT JOIN vehicle_team vt ON vt.id = dvt.vehicle_team_id
                LEFT JOIN 
                (SELECT o.primary_driver_id as driver_id, COUNT(DISTINCT o.id) as tong_don FROM orders o
                WHERE o.del_flag = 0
                AND DATE(o.ETD_date) >= '" . $from_date . "' AND DATE(o.ETD_date) <= '" . $to_date . "'
                GROUP BY o.primary_driver_id ) tmp1 ON  tmp1.driver_id = d.id
                LEFT JOIN
                (SELECT tmp.id as driver_id,
                SUM(CASE WHEN tmp.order_status = 3 THEN 1 ELSE 0 END) as xac_nhan,
                SUM(CASE WHEN tmp.order_status = 4 THEN 1 ELSE 0 END) as nhan_hang,
                SUM(CASE WHEN tmp.order_status = 5 THEN 1 ELSE 0 END) as tra_hang
                FROM ( SELECT DISTINCT d.id, oh.order_id, oh.order_status
                FROM order_history oh
                INNER JOIN admin_users au ON oh.ins_id = au.id
                INNER JOIN drivers d ON au.id = d.user_id
                INNER JOIN orders o ON o.id = oh.order_id
                WHERE oh.del_flag = 0 AND au.del_flag = 0  AND d.del_flag = 0 AND au.role = 'driver'
                AND o.del_flag = 0
                AND o.primary_driver_id = d.id
                AND DATE(o.ETD_date) >= '" . $from_date . "' AND DATE(o.ETD_date) <= '" . $to_date . "'
                ) tmp
                GROUP BY tmp.id ) tmp2 ON d.id = tmp2.driver_id 
                WHERE d.del_flag = 0 AND au.del_flag = 0 AND tmp1.tong_don > 0
                ORDER BY vt.name";

        $results = DB::select(DB::raw($sql));
        return $results;
    }
}
