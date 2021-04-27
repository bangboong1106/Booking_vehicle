<?php

namespace App\Repositories;

use App\Model\Entities\Customer;
use App\Model\Entities\ReportData;
use App\Repositories\Base\CustomRepository;
use App\Validators\ReportDataValidator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReportDataRepository extends CustomRepository
{
    function model()
    {
        return ReportData::class;
    }

    function validator()
    {
        return ReportDataValidator::class;
    }

    public function getReportData($vehicleIDs)
    {
        if (empty($vehicleIDs)) {
            return $this->search()->get();
        } else {
            $arr = explode(',', $vehicleIDs);
//            $map = collect($arr)->map(function ($vehicleID) {
//                return '\'' . $vehicleID . '\'';
//
//            });
//            $result = $map->implode(',');
            return DB::table('report_data')
                ->whereIn('vehicle', $arr)
                ->get();
        }
    }

    public function reportOperatorDaily()
    {
        $procedureName = 'proc_report_operating_daily';
        $sql = 'call ' . $procedureName . '()';

        DB::select($sql);
    }

    public function reportIncomeByTime($from_date, $to_date, $per = false, $dayCondition)
    {
        $procedureName = 'proc_report_by_income';
        $sql = 'call ' . $procedureName . '(?,?,?,?,?)';

        $partnerId = Auth::user()->partner_id;

        $query = DB::select($sql, array(1, $from_date, $to_date, $dayCondition, $partnerId));
        $labels = [];
        $data = [];
        $total = 0;
        foreach ($query as $item) {
            $labels[] = $item->date;
            $total_value = $item->total ? (float)$item->total : 0;
            $data[] = $total_value;
            $total += $total_value;
        }

        $extraRevenue = 0;
        $extraRevenuePer = 0;
        $extraRevenuePerType = 0;
        if ($per) {
            $diff = strtotime($from_date) - strtotime($to_date);
            $dateDiff = abs(round($diff / (60 * 60 * 24)));
            $dateDiff += 1;
            $from_date_prev = date('Y-m-d', strtotime('-' . $dateDiff . ' day', strtotime($from_date)));
            $to_date_prev = date('Y-m-d', strtotime('-1 day', strtotime($from_date)));

            $query_prev = DB::select($sql, array(1, $from_date_prev, $to_date_prev));
            $total_pev = 0;
            foreach ($query_prev as $item) {
                $total_pev += $item->total ? (float)$item->total : 0;
            }

            $extraRevenue = abs($total - $total_pev);
            if ($total_pev != 0) {
                $extraRevenuePer = ($extraRevenue / $total_pev) * 100;
                $extraRevenuePerType = $total >= $total_pev ? 1 : -1;
            } else {
                $extraRevenuePer = 0;
                $extraRevenuePerType = 0;
            }
        }

        $dataSourceRevenue = [
            'labels' => $labels, // Danh sách các ngày tính doanh thu (from - to date)
            'datasets' => [
                'data' => $data
            ],
            'total' => $total,
            'extraRevenue' => $extraRevenue, // Tổng Chênh lệch doanh thu
            'extraRevenuePer' => $extraRevenuePer, // Tổng Chêch lệch doanh thu tính theo %
            'extraRevenuePerType' => $extraRevenuePerType // Loại Tổng Doanh thu tăng là 1, giảm là -1
        ];
        return $dataSourceRevenue;
    }

    public function reportIncomeCostProfitByTime($from_date, $to_date, $dayCondition)
    {
        $procedureName = 'proc_report_income_cost_profit_by_time';
        $sql = 'call ' . $procedureName . '(?,?,?,?)';

        $partnerId = Auth::user()->partner_id;

        $query = DB::select($sql, array($from_date, $to_date, $dayCondition, $partnerId));
        $labels = [];
        $data = [];
        $totalIncome = 0;
        $totalCost = 0;
        $totalProfit = 0;
        foreach ($query as $item) {
            $labels[] = $item->date;
            $incomeValue = $item->income ? (float)$item->income : 0;
            $costValue = $item->cost ? (float)$item->cost : 0;
            $profitValue = $item->profit ? (float)$item->profit : 0;
            $totalIncome += $incomeValue;
            $totalCost += $costValue;
            $totalProfit += $profitValue;
            $data[] = [
                'income' => $incomeValue,
                'cost' => $costValue,
                'profit' => $profitValue
            ];
        }

        $dataSourceRevenue = [
            'labels' => $labels, // Danh sách các ngày tính doanh thu (from - to date)
            'datasets' => [
                'data' => $data
            ],
            'totalIncome' => $totalIncome,
            'totalCost' => $totalCost,
            'totalProfit' => $totalProfit,
        ];
        return $dataSourceRevenue;
    }

    public function reportTurnByTime($from_date, $to_date)
    {
        $procedureName = 'proc_report_by_turn';
        $sql = 'call ' . $procedureName . '(?,?,?,?)';

        $partnerId = Auth::user()->partner_id;

        $query = DB::select($sql, array(1, $from_date, $to_date, $partnerId));

        $labels = [];
        $data = [];
        $total = 0;
        foreach ($query as $item) {
            $labels[] = $item->date;
            $total_value = $item->total ? (float)$item->total : 0;
            $data[] = $total_value;
            $total += $total_value;
        }

        $dataSourceOrderCus = [
            'labels' => $labels,
            'datasets' => [
                'data' => $data
            ],
            'total' => $total,
        ];
        return $dataSourceOrderCus;
    }

    public function reportTurnByCustomer($from_date, $to_date, $limit = 10, $per = false, $realTime = true)
    {
        $partnerId = Auth::user()->partner_id;
        if (!$realTime) {
            $sql = 'SELECT label as entity_name, value as total FROM report_operating_daily WHERE date = 1 AND type = 1 ';
            if ($partnerId)
                $sql .= ' AND partner_id = ' . $partnerId;
            $query = DB::select($sql);
        } else {
            $procedureName = 'proc_report_by_turn';
            $sql = 'call ' . $procedureName . '(?,?,?,?)';
            $query = DB::select($sql, array(0, $from_date, $to_date, $partnerId));
        }

        $datas = [];
        foreach ($query as $i => $item) {
            if (array_key_exists($item->entity_name, $datas)) {
                $datas[$item->entity_name] += $item->total ? $item->total : 0;
            } else {
                $datas[$item->entity_name] = $item->total ? $item->total : 0;
            }
        }
        arsort($datas);

        $labels = [];
        $data = [];
        $labelCustomerTurns = [];
        $dataCustomerTurn = [];
        $total = 0;
        $totalCustomer = 0;
        $i = 0;
        foreach ($datas as $key => $value) {
            if ($i < $limit) {
                $labels[] = $key;
                $data[] = $value;
            }
            $total += $value;
            if ($value > 0) {
                $labelCustomerTurns[] = $key;
                $dataCustomerTurn[] = $value;
                $totalCustomer++;
            }
            $i++;
        }

        $extraOrder = 0;
        $extraOrderPer = 0;
        $extraOrderPerType = 0;
        $extraCustomer = 0;
        $extraCustomerPer = 0;
        $extraCustomerPerType = 0;

        if ($per) {
            if (!$realTime) {
                $sql = 'SELECT label as entity_name, value as total FROM report_operating_daily WHERE date = 2 AND type = 1';
                $query_prev = DB::select($sql);
                if ($partnerId)
                    $sql .= ' AND partner_id = ' . $partnerId;
                $query_prev = DB::select($sql);
            } else {
                $procedureName = 'proc_report_by_turn';
                $sql = 'call ' . $procedureName . '(?,?,?,?)';

                $diff = strtotime($from_date) - strtotime($to_date);
                $dateDiff = abs(round($diff / (60 * 60 * 24)));
                $dateDiff += 1;
                $from_date_prev = date('Y-m-d', strtotime('-' . $dateDiff . ' day', strtotime($from_date)));
                $to_date_prev = date('Y-m-d', strtotime('-1 day', strtotime($from_date)));

                $query_prev = DB::select($sql, array(0, $from_date_prev, $to_date_prev, $partnerId));
            }


            $datas_pev = [];
            foreach ($query_prev as $item) {
                if (array_key_exists($item->entity_name, $datas_pev)) {
                    $datas_pev[$item->entity_name] += $item->total ? $item->total : 0;
                } else {
                    $datas_pev[$item->entity_name] = $item->total ? $item->total : 0;
                }
            }

            $total_pev = 0;
            $totalCustomer_prev = 0;
            foreach ($datas_pev as $key => $value) {
                $total_pev += $value;
                if ($value > 0) {
                    $totalCustomer_prev++;
                }
            }

            $extraOrder = abs($total - $total_pev);
            if ($total_pev != 0) {
                $extraOrderPer = ($extraOrder / $total_pev) * 100;
                $extraOrderPerType = $total >= $total_pev ? 1 : -1;
            } else {
                if ($total != 0)
                    $extraOrderPer = 100;
                else
                    $extraOrderPer = 0;
                $extraOrderPerType = 1;
            }

            $extraCustomer = abs($totalCustomer - $totalCustomer_prev);
            if ($totalCustomer_prev != 0) {
                $extraCustomerPer = ($extraCustomer / $totalCustomer_prev) * 100;
                $extraCustomerPerType = $totalCustomer >= $totalCustomer_prev ? 1 : -1;
            } else {
                if ($totalCustomer != 0)
                    $extraCustomerPer = 100;
                else
                    $extraCustomerPer = 0;
                $extraCustomerPerType = 1;
            }
        }

        $dataSourceOrderCus = [
            'labels' => $labels, // Danh sách các khách hàng
            'datasets' => [
                'data' => $data
            ],
            'labelCustomerTurns' => $labelCustomerTurns, // Danh sách khách hàng có phát sinh đơn hàng
            'dataCustomerTurn' => [
                'data' => $dataCustomerTurn
            ],
            'total' => $total,
            'extraOrder' => $extraOrder, // Tổng Chênh lệch số lượng đơn hàng
            'extraOrderPer' => $extraOrderPer, // Tổng Chêch lệch tỉ lệ đơn hàng tính theo %
            'extraOrderPerType' => $extraOrderPerType, // Loại tổng chêch lệch đơn hàng tăng là 1, giảm là -1
            'totalCustomer' => $totalCustomer,
            'extraCustomer' => $extraCustomer, // Tổng Chênh lệch số khách hàng phát sinh đơn hàng
            'extraCustomerPer' => $extraCustomerPer, // Tổng Chêch lệch tỉ lệ khách hàng tính theo %
            'extraCustomerPerType' => $extraCustomerPerType // Loại tổng chêch lệch khách hàng tăng là 1, giảm là -1
        ];
        return $dataSourceOrderCus;
    }

    public function reportIncomeByCustomer($from_date, $to_date, $limit = 10, $per = false, $realTime = true, $dayCondition)
    {
        $partnerId = Auth::user()->partner_id;
        if (!$realTime) {
            $sql = 'SELECT label as entity_name';
            if ($dayCondition == 1) {
                $sql .= ', amount_ETD as status_complete';
            } else if ($dayCondition == 2) {
                $sql .= ', amount_ETD_reality as status_complete';
            } else if ($dayCondition == 3) {
                $sql .= ', amount_ETA as status_complete';
            } else if ($dayCondition == 4) {
                $sql .= ', amount_ETA_reality as status_complete';
            }
            $sql .= ' FROM report_operating_daily WHERE date = 1 AND type = 2 ';
            if ($partnerId)
                $sql .= ' AND partner_id = ' . $partnerId;

            $query = DB::select($sql);
            $query = collect($query)->sortBy('status_complete')->reverse()->toArray();
        } else {
            $procedureName = 'proc_report_by_income';
            $sql = 'call ' . $procedureName . '(?,?,?,?,?)';
            $query = DB::select($sql, array(0, $from_date, $to_date, $dayCondition, $partnerId));
            $query = collect($query)->sortBy('status_complete')->reverse()->toArray();
        }

        $datas = [];
        foreach ($query as $item) {
            if (array_key_exists($item->entity_name, $datas)) {
                $datas[$item->entity_name] += $item->status_complete ? $item->status_complete : 0;
            } else {
                $datas[$item->entity_name] = $item->status_complete ? $item->status_complete : 0;
            }
        }

        arsort($datas);

        $labels = [];
        $data = [];
        $total = 0;
        $i = 0;
        foreach ($datas as $key => $value) {
            if ($i < $limit) {
                $labels[] = $key;
                $data[] = $value;
            }
            $total += $value;
            $i++;
        }

        $extraRevenue = 0;
        $extraRevenuePer = 0;
        $extraRevenuePerType = 0;
        if ($per) {
            if (!$realTime) {
                $sql = 'SELECT label as entity_name';
                if ($dayCondition == 1) {
                    $sql .= ', amount_ETD as status_complete';
                } else if ($dayCondition == 2) {
                    $sql .= ', amount_ETD_reality as status_complete';
                } else if ($dayCondition == 3) {
                    $sql .= ', amount_ETA as status_complete';
                } else if ($dayCondition == 4) {
                    $sql .= ', amount_ETA_reality as status_complete';
                }
                $sql .= ' FROM report_operating_daily WHERE date = 2 AND type = 2';
                if ($partnerId)
                    $sql .= ' AND partner_id = ' . $partnerId;
                $query_prev = DB::select($sql);
            } else {
                $diff = strtotime($from_date) - strtotime($to_date);
                $dateDiff = abs(round($diff / (60 * 60 * 24)));
                $dateDiff += 1;
                $from_date_prev = date('Y-m-d', strtotime('-' . $dateDiff . ' day', strtotime($from_date)));
                $to_date_prev = date('Y-m-d', strtotime('-1 day', strtotime($from_date)));

                $query_prev = DB::select($sql, array(0, $from_date_prev, $to_date_prev, $dayCondition, $partnerId));
            }

            $datas_prev = [];
            foreach ($query_prev as $item) {
                if (array_key_exists($item->entity_name, $datas_prev)) {
                    $datas_prev[$item->entity_name] += $item->status_complete ? $item->status_complete : 0;
                } else {
                    $datas_prev[$item->entity_name] = $item->status_complete ? $item->status_complete : 0;
                }
            }

            $total_pev = 0;
            foreach ($datas_prev as $key => $value) {
                $total_pev += $value;
            }

            $extraRevenue = abs($total - $total_pev);
            if ($total_pev != 0) {
                $extraRevenuePer = ($extraRevenue / $total_pev) * 100;
                $extraRevenuePerType = $total >= $total_pev ? 1 : -1;
            } else {
                if ($total != 0)
                    $extraRevenuePer = 100;
                else
                    $extraRevenuePer = 0;
                $extraRevenuePerType = 1;

            }
        }

        $dataSourceRevenueCus = [
            'labels' => $labels, // Danh sách các công ty khách hàng
            'datasets' => [
                'data' => $data
            ],
            'total' => $total,
            'extraRevenue' => $extraRevenue,
            'extraRevenuePer' => $extraRevenuePer,
            'extraRevenuePerType' => $extraRevenuePerType
        ];
        return $dataSourceRevenueCus;
    }

    public function reportByCustomer($from_date, $to_date)
    {
        $partnerId = Auth::user()->partner_id;
        $query = Customer::whereBetween('ins_date', [$from_date . ' 00:00:00', $to_date . ' 23:59:59']);
        if ($partnerId) {
            $query = $query->where('partner_id', '=', $partnerId);
        }
        $query = $query->orderBy('ins_date', 'DESC')->get();
        $labels = [];
        foreach ($query as $entity) {
            $labels[] = $entity->customer_code . '|||' . $entity->full_name;
        }
        $total = count($query);

        $diff = strtotime($from_date) - strtotime($to_date);
        $dateDiff = abs(round($diff / (60 * 60 * 24)));
        $dateDiff += 1;
        $from_date_prev = date('Y-m-d', strtotime('-' . $dateDiff . ' day', strtotime($from_date)));
        $to_date_prev = date('Y-m-d', strtotime('-1 day', strtotime($from_date)));

        $query_prev = Customer::whereBetween('ins_date', [$from_date_prev . ' 00:00:00', $to_date_prev . ' 23:59:59']);
        if ($partnerId) {
            $query_prev = $query_prev->where('partner_id', '=', $partnerId);
        }
        $query_prev = $query_prev->where('partner_id', '=', $partnerId)->get();
        $total_pev = count($query_prev);

        $extra = abs($total - $total_pev);
        if ($total_pev != 0) {
            $extraPer = ($extra / $total_pev) * 100;
            $extraPerType = $total >= $total_pev ? 1 : -1;
        } else {
            if ($total != 0)
                $extraPer = 100;
            else
                $extraPer = 0;
            $extraPerType = 1;
        }
        return [
            'labels' => $labels,
            'total' => $total,
            'extra' => $extra,
            'extraPer' => $extraPer,
            'extraPerType' => $extraPerType];
    }

    public function reportByOrder($from_date, $to_date)
    {
        $partnerId = Auth::user()->partner_id;
        $sql = "SELECT tmp.status, COUNT(tmp.id) as total
                            FROM (
                                SELECT o.partner_id,o.id, o.status FROM orders o 
                                WHERE o.del_flag = 0 AND o.status != 1 
                                AND CASE WHEN o.ETA_date_reality IS NOT NULL AND o.status = 5 
                                        THEN o.ETA_date_reality BETWEEN :from_date1 AND :to_date1 
		                                ELSE o.ETA_date BETWEEN :from_date2 AND :to_date2 END
                                UNION 
                                SELECT o.partner_id,o.id, o.status FROM orders o 
                                WHERE o.del_flag = 0 AND o.status = 1
                                AND DATE(o.ins_date) BETWEEN :from_date3 AND :to_date3
                                ) tmp ";
        if ($partnerId)
            $sql .= " WHERE tmp.partner_id = " . $partnerId;

        $sql .= " GROUP BY tmp.status";

        $query = DB::select(DB::raw($sql)
            , array('from_date1' => $from_date, 'to_date1' => $to_date,
                'from_date2' => $from_date, 'to_date2' => $to_date,
                'from_date3' => $from_date, 'to_date3' => $to_date));

        return $query;
    }

    public function getTopOrderIncome($from_date, $to_date, $dayCondition)
    {
        $partnerId = Auth::user()->partner_id;
        $sql = " SELECT o.order_code, o.amount FROM orders o WHERE o.del_flag = 0 ";
        if ($dayCondition == 1) {
            $sql .= "AND o.status IN (2,3,4,5,7) AND o.ETD_date BETWEEN :from_date AND :to_date";
        } else if ($dayCondition == 2) {
            $sql .= "AND o.status IN (4,5) AND o.ETD_date_reality BETWEEN :from_date AND :to_date";
        } else if ($dayCondition == 3) {
            $sql .= "AND o.status IN (2,3,4,5,7) AND o.ETA_date BETWEEN :from_date AND :to_date";
        } else {
            $sql .= "AND o.status = 5 AND o.ETA_date_reality BETWEEN :from_date AND :to_date";
        }
        if ($partnerId)
            $sql .= " AND partner_id = " . $partnerId;
        $sql .= " ORDER BY o.amount DESC LIMIT 20";
        $query = DB::select(DB::raw($sql), array('from_date' => $from_date, 'to_date' => $to_date));

        return $query;
    }

    public function reportByDocument($from_date, $to_date, $dayCondition, $customerIds = null)
    {
        $partnerId = Auth::user()->partner_id;
        $sql = " SELECT COUNT(o.id) as total
                            , SUM(CASE WHEN o.status_collected_documents != 2 OR  o.status_collected_documents IS NULL THEN 1 ELSE 0 END) total_not_collect
                            , SUM(CASE WHEN o.status_collected_documents = 2 THEN 1 ELSE 0 END) total_collect 
                            , SUM(CASE WHEN o.status_collected_documents = 3 THEN 1 ELSE 0 END) total_out_of_date
                            , SUM(CASE WHEN o.status_collected_documents = 4 THEN 1 ELSE 0 END) total_next_day
                            , SUM(CASE WHEN o.status_collected_documents = 5 THEN 1 ELSE 0 END) total_today
                            , SUM(CASE WHEN o.status_collected_documents = 2 AND o.date_collected_documents IS NOT NULL
								AND o.date_collected_documents_reality IS NOT NULL 
								AND DATE_FORMAT(CONCAT(o.date_collected_documents_reality,' ',o.time_collected_documents_reality),'%Y-%m-%d %H:%i') 
								> DATE_FORMAT(CONCAT(o.date_collected_documents,' ',o.time_collected_documents),'%Y-%m-%d %H:%i') THEN 1 ELSE 0 END) total_collect_late
                            , SUM(CASE WHEN o.status_collected_documents = 2 AND o.date_collected_documents IS NOT NULL
								AND o.date_collected_documents_reality IS NOT NULL 
								AND DATE_FORMAT(CONCAT(o.date_collected_documents_reality,' ',o.time_collected_documents_reality),'%Y-%m-%d %H:%i') 
								<= DATE_FORMAT(CONCAT(o.date_collected_documents,' ',o.time_collected_documents),'%Y-%m-%d %H:%i') THEN 1 ELSE 0 END) total_collect_on_time						
                            , SUM(CASE WHEN o.status_collected_documents = 2 AND o.date_collected_documents IS NOT NULL  AND o.date_collected_documents < o.date_collected_documents_reality  
                                THEN DATEDIFF(o.date_collected_documents_reality, o.date_collected_documents) 
                                WHEN o.status_collected_documents != 2 AND o.date_collected_documents IS NOT NULL AND o.date_collected_documents < CURDATE()
                                THEN DATEDIFF(CURDATE(), o.date_collected_documents) ELSE 0 END) total_day_late
                      FROM orders o 
                      WHERE o.del_flag = 0 ";
        if ($dayCondition == 1) {
            $sql .= " AND o.status IN (2,3,4,5,7) AND o.ETD_date BETWEEN :from_date AND :to_date";
        } else if ($dayCondition == 2) {
            $sql .= " AND o.status IN (4,5) AND o.ETD_date_reality BETWEEN :from_date AND :to_date";
        } else if ($dayCondition == 3) {
            $sql .= " AND o.status IN (2,3,4,5,7) AND o.ETA_date BETWEEN :from_date AND :to_date";
        } else {
            $sql .= " AND o.status = 5 AND o.ETA_date_reality BETWEEN :from_date AND :to_date";
        }
        if ($partnerId)
            $sql .= " AND partner_id = " . $partnerId;

        if ($customerIds && count($customerIds) > 0) {
            $sql .= " AND o.customer_id IN (" . implode(',', $customerIds) . ")";
            $sql .= " GROUP BY o.customer_id";
        }
        $query = DB::select(DB::raw($sql)
            , array('from_date' => $from_date, 'to_date' => $to_date));

        return $query;
    }

    // Lấy báo cáo số lượng hàng hoá theo thời gian
    // CreatedBy nlhoang 28/08/2020
    public function reportGoodsByTime($from_date, $to_date, $dayCondition)
    {
        $procedureName = 'proc_report_goods_by_time';
        $sql = 'call ' . $procedureName . '(?,?,?,?)';

        $partnerId = Auth::user()->partner_id;

        $query = DB::select($sql, array($from_date, $to_date, $dayCondition, $partnerId));

        $labels = [];
        $data = [];
        $totalVolume = 0;
        $totalWeight = 0;
        foreach ($query as $item) {
            $labels[] = $item->date;
            $volumeValue = $item->volume ? (float)$item->volume : 0;
            $weightValue = $item->weight ? (float)$item->weight : 0;
            $totalVolume += $volumeValue;
            $totalWeight += $weightValue;
            $data[] = [
                'volume' => $volumeValue,
                'weight' => $weightValue,
            ];
        }

        $dataSource = [
            'labels' => $labels,
            'datasets' => [
                'data' => $data
            ],
            'totalVolume' => $totalVolume,
            'totalWeight' => $totalWeight,
        ];
        return $dataSource;
    }
}