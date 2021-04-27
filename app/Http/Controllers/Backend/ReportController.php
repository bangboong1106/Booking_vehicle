<?php

namespace App\Http\Controllers\Backend;

use App\Exports\ReportExport;
use App\Http\Controllers\Base\BackendController;
use App\Repositories\ReportDataRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;


/**
 * Class ReportController
 * @package App\Http\Controllers\Backend
 */
class ReportController extends BackendController
{
    public function __construct(ReportDataRepository $demoReportDataRepository)
    {
        parent::__construct();
        $this->setRepository($demoReportDataRepository);
        $this->setMenu('report');
//        $this->setTitle(trans('models.report.attributes.title'));
    }

    public function index()
    {
        $this->setTitle('Báo cáo tình trạng hoạt động');
        $dayCondition = env('DAY_CONDITION_DEFAULT', 4);
        $this->setViewData([
            'dayCondition' => $dayCondition
        ]);
        return $this->render();
    }

    public function getReportData()
    {
        $entityType = Request::get('EntityType');
        $displayType = Request::get('DisplayType');
        $vehicleTeamIDs = empty(Request::get('VehicleTeamIDs')) ? null : Request::get('VehicleTeamIDs');
        $vehicleIDs = empty(Request::get('VehicleIDs')) ? null : Request::get('VehicleIDs');
        $driverIDs = empty(Request::get('DriverIDs')) ? null : Request::get('DriverIDs');
        $customerIDs = empty(Request::get('CustomerIDs')) ? null : Request::get('CustomerIDs');
        $reportType = Request::get('ReportType');
        $fromDate = Request::get('FromDate');
        $toDate = Request::get('ToDate');
        $staticBy = Request::get('StatisticBy');
        $report = [];
        $summary = 1;
        $dayCondition = empty(Request::get('DayCondition')) ? env('DAY_CONDITION_DEFAULT', 4) : Request::get('DayCondition');
        $partnerId = Auth::user()->role == 'admin' ? Request::get('PartnerIds', null) : Auth::user()->partner_id;

        /*if (empty($reportType) || strlen($reportType) == 1) {
            $summary = 1;
        }*/

        switch ($displayType) {
            case 1:
                $status_all = 1;
                $status_incomplete = 0;
                $status_complete = 0;
                $status_cancel = 0;
                /*$status_future = 0;
                $status_on_time = 0;
                $status_late = 0;*/
                if (empty($reportType)) {
                    $reportType = '0,1,2,6';
                }
                $reportType = explode(',', $reportType);
                if ($reportType && is_array($reportType)) {
                    foreach ($reportType as $type) {
                        switch ($type) {
                            /*case 0:
                                $status_all = 1;
                                break;*/
                            case 1:
                                $status_incomplete = 1;
                                break;
                            case 2:
                                $status_complete = 1;
                                break;
                            /*  case 3:
                                  $status_future = 1;
                                  break;
                              case 4:
                                  $status_on_time = 1;
                                  break;
                              case 5:
                                  $status_late = 1;
                                  break;*/
                            case 6:
                                $status_cancel = 1;
                                break;
                        }
                    }
                }
                $report = $this->getSQLReportByTurn($summary, $entityType, $staticBy, $vehicleTeamIDs, $vehicleIDs, $driverIDs, $customerIDs, $status_all
                    , $status_incomplete, $status_complete, $status_cancel, $fromDate, $toDate, $partnerId);
                break;
            case 2:
                $report = $this->getSQLReportByIncome($summary, $entityType, $staticBy, $vehicleTeamIDs, $vehicleIDs, $driverIDs, $customerIDs, $fromDate, $toDate, $dayCondition, $partnerId);
                break;
            case 3:
                $report = $this->getSQLReportByCost($summary, $entityType, $staticBy, $vehicleTeamIDs, $vehicleIDs, $driverIDs, $customerIDs, $fromDate, $toDate, $dayCondition, $partnerId);
                break;
            case 4:
                $report = $this->getSQLReportByProfit($summary, $entityType, $staticBy, $vehicleTeamIDs, $vehicleIDs, $driverIDs, $customerIDs, $fromDate, $toDate, $dayCondition, $partnerId);
                break;
        }
        return $report;
    }

    private function getSQLReportByTurn($summary, $entityType, $staticBy, $vehicle_team_ids, $vehicle_ids, $driver_ids, $customer_ids, $status_all
        , $status_incomplete, $status_complete, $status_cancel, $from_date, $to_date, $partnerId)
    {
        $procedureName = '';
        switch ($entityType) {
            case 1:
                if ($staticBy == 'day') {
                    $procedureName = 'proc_report_vehicle_team_by_turn';
                } elseif ($staticBy == 'month') {
                    $procedureName = 'proc_report_vehicle_team_by_turn_monthly';
                }
                break;
            case 2:
                if ($staticBy == 'day') {
                    $procedureName = 'proc_report_vehicle_by_turn';
                } elseif ($staticBy == 'month') {
                    $procedureName = 'proc_report_vehicle_by_turn_monthly';
                }
                break;
            case 3:
                if ($staticBy == 'day') {
                    $procedureName = 'proc_report_driver_by_turn';
                } elseif ($staticBy == 'month') {
                    $procedureName = 'proc_report_driver_by_turn_monthly';
                }
                break;
            case 4:
                if ($staticBy == 'day') {
                    $procedureName = 'proc_report_customer_by_turn';
                } elseif ($staticBy == 'month') {
                    $procedureName = 'proc_report_customer_by_turn_monthly';
                }
                break;
        }
        $sql = 'call ' . $procedureName . '(?,?,?,?,?,?,?,?,?,?,?,?)';
        $query = DB::select($sql, array(0, $vehicle_team_ids, $vehicle_ids, $driver_ids, $customer_ids, $status_all, $status_incomplete,
            $status_complete, $status_cancel, $from_date, $to_date, $partnerId));

        $summaryQuery = null;
        if ($summary == 1)
            $summaryQuery = DB::select($sql, array($summary, $vehicle_team_ids, $vehicle_ids, $driver_ids, $customer_ids, $status_all, $status_incomplete,
                $status_complete, $status_cancel, $from_date, $to_date, $partnerId));

        $results = json_encode(["data" => $query, "summary" => $summaryQuery], JSON_NUMERIC_CHECK);
        return $results;
    }

    private function getSQLReportByIncome($summary, $entityType, $staticBy, $vehicle_team_ids, $vehicle_ids, $driver_ids, $customer_ids, $from_date, $to_date, $dayCondition, $partnerId)
    {
        $procedureName = '';
        switch ($entityType) {
            case 1:
                if ($staticBy == 'day') {
                    $procedureName = 'proc_report_vehicle_team_by_income';
                } elseif ($staticBy == 'month') {
                    $procedureName = 'proc_report_vehicle_team_by_income_monthly';
                }
                break;
            case 2:
                if ($staticBy == 'day') {
                    $procedureName = 'proc_report_vehicle_by_income';
                } elseif ($staticBy == 'month') {
                    $procedureName = 'proc_report_vehicle_by_income_monthly';
                }
                break;
            case 3:
                if ($staticBy == 'day') {
                    $procedureName = 'proc_report_driver_by_income';
                } elseif ($staticBy == 'month') {
                    $procedureName = 'proc_report_driver_by_income_monthly';
                }
                break;
            case 4:
                if ($staticBy == 'day') {
                    $procedureName = 'proc_report_customer_by_income';
                } elseif ($staticBy == 'month') {
                    $procedureName = 'proc_report_customer_by_income_monthly';
                }
                break;
        }
        $sql = 'call ' . $procedureName . '(?,?,?,?,?,?,?,?,?)';
        $query = DB::select($sql, array(0, $vehicle_team_ids, $vehicle_ids, $driver_ids, $customer_ids, $from_date, $to_date, $dayCondition, $partnerId));

        $summaryQuery = null;
        if ($summary == 1)
            $summaryQuery = DB::select($sql, array($summary, $vehicle_team_ids, $vehicle_ids, $driver_ids, $customer_ids, $from_date, $to_date, $dayCondition, $partnerId));

        $results = json_encode(["data" => $query, "summary" => $summaryQuery], JSON_NUMERIC_CHECK);
        return $results;
    }

    private function getSQLReportByCost($summary, $entityType, $staticBy, $vehicle_team_ids, $vehicle_ids, $driver_ids, $customer_ids, $from_date, $to_date, $dayCondition, $partnerId)
    {
        $procedureName = '';
        switch ($entityType) {
            case 1:
                if ($staticBy == 'day') {
                    $procedureName = 'proc_report_vehicle_team_by_cost';
                } elseif ($staticBy == 'month') {
                    $procedureName = 'proc_report_vehicle_team_by_cost_monthly';
                }
                break;
            case 2:
                if ($staticBy == 'day') {
                    $procedureName = 'proc_report_vehicle_by_cost';
                } elseif ($staticBy == 'month') {
                    $procedureName = 'proc_report_vehicle_by_cost_monthly';
                }
                break;
            case 3:
                if ($staticBy == 'day') {
                    $procedureName = 'proc_report_driver_by_cost';
                } elseif ($staticBy == 'month') {
                    $procedureName = 'proc_report_driver_by_cost_monthly';
                }
                break;
            case 4:
                if ($staticBy == 'day') {
                    $procedureName = 'proc_report_customer_by_cost';
                } elseif ($staticBy == 'month') {
                    $procedureName = 'proc_report_customer_by_cost_monthly';
                }
                break;
        }
        $sql = 'call ' . $procedureName . '(?,?,?,?,?,?,?,?,?)';
        $query = DB::select($sql, array(0, $vehicle_team_ids, $vehicle_ids, $driver_ids, $customer_ids, $from_date, $to_date, $dayCondition, $partnerId));

        $summaryQuery = null;
        if ($summary == 1)
            $summaryQuery = DB::select($sql, array($summary, $vehicle_team_ids, $vehicle_ids, $driver_ids, $customer_ids, $from_date, $to_date, $dayCondition, $partnerId));

        $results = json_encode(["data" => $query, "summary" => $summaryQuery], JSON_NUMERIC_CHECK);
        return $results;
    }

    private function getSQLReportByProfit($summary, $entityType, $staticBy, $vehicle_team_ids, $vehicle_ids, $driver_ids, $customer_ids, $from_date, $to_date, $dayCondition, $partnerId)
    {
        $procedureName = '';
        switch ($entityType) {
            case 1:
                if ($staticBy == 'day') {
                    $procedureName = 'proc_report_vehicle_team_by_profit';
                } elseif ($staticBy == 'month') {
                    $procedureName = 'proc_report_vehicle_team_by_profit_monthly';
                }
                break;
            case 2:
                if ($staticBy == 'day') {
                    $procedureName = 'proc_report_vehicle_by_profit';
                } elseif ($staticBy == 'month') {
                    $procedureName = 'proc_report_vehicle_by_profit_monthly';
                }
                break;
            case 3:
                if ($staticBy == 'day') {
                    $procedureName = 'proc_report_driver_by_profit';
                } elseif ($staticBy == 'month') {
                    $procedureName = 'proc_report_driver_by_profit_monthly';
                }
                break;
            case 4:
                if ($staticBy == 'day') {
                    $procedureName = 'proc_report_customer_by_profit';
                } elseif ($staticBy == 'month') {
                    $procedureName = 'proc_report_customer_by_profit_monthly';
                }
                break;
        }
        $sql = 'call ' . $procedureName . '(?,?,?,?,?,?,?,?,?)';
        $query = DB::select($sql, array(0, $vehicle_team_ids, $vehicle_ids, $driver_ids, $customer_ids, $from_date, $to_date, $dayCondition, $partnerId));

        $summaryQuery = null;
        if ($summary == 1)
            $summaryQuery = DB::select($sql, array($summary, $vehicle_team_ids, $vehicle_ids, $driver_ids, $customer_ids, $from_date, $to_date, $dayCondition, $partnerId));

        $results = json_encode(["data" => $query, "summary" => $summaryQuery], JSON_NUMERIC_CHECK);
        return $results;
    }
}
