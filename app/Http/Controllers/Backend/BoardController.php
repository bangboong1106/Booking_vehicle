<?php

namespace App\Http\Controllers\Backend;

use App\Common\AppConstant;
use App\Common\HttpCode;
use App\Exports\ReportSchedulerExport;
use App\Http\Controllers\Base\BackendController;
use App\Repositories\ReportDataRepository;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Mockery\Exception;

class BoardController extends BackendController
{
    protected $_reportRepository;

    /**
     * @return ReportDataRepository
     */
    public function getReportDataRepository()
    {
        return $this->_reportRepository;
    }

    /**
     * @param $reportRepository
     */
    public function setReportDataRepository($reportRepository): void
    {
        $this->_reportRepository = $reportRepository;
    }

    public function __construct(ReportDataRepository $reportDataRepository)
    {
        parent::__construct();
        $this->setReportDataRepository($reportDataRepository);
        $this->setMenu('board');
    }

    public function _checkPermission($action = 'view') {
        if ( \Auth::user()->role == 'partner') {
            $this->_redirectToHome()->send();
        }
        return true;
    }

    public function index()
    {
        $this->_checkPermission();
        $this->setTitle(trans('Tổng quan'));
        $dayCondition = env('DAY_CONDITION_DEFAULT', 4);
        $this->setViewData([
            'dayCondition' => $dayCondition
        ]);
        return $this->render();
    }

    public function generalInfoOrder(Request $request)
    {
        try {
            $fromDate = $request['fromDate'];
            $toDate = $request['toDate'];
            $realTime = isset($request['realTime']) && $request['realTime'] == 'true' ? true : false;

            $limit = 10;

            $turnByCustomer = $this->getReportDataRepository()->reportTurnByCustomer($fromDate, $toDate, $limit, true, $realTime);
            $totalOrderStatus = $this->getReportDataRepository()->reportByOrder($fromDate, $toDate);

            $dataSource = [
                'order' => [
                    'total' => $turnByCustomer['total'],
                    'extra' => $turnByCustomer['extraOrder'],
                    'extraPer' => $turnByCustomer['extraOrderPer'],
                    'extraPerType' => $turnByCustomer['extraOrderPerType'],
                    'status' => $totalOrderStatus
                ]
            ];

            return json_encode((object)[
                'errorCode' => HttpCode::EC_OK,
                'errorMessage' => '',
                'data' => $dataSource
            ], JSON_NUMERIC_CHECK);
        } catch (Exception $exception) {
            logError($exception);
            return response()->json([
                'errorCode' => HttpCode::EC_APPLICATION_ERROR,
                'errorMessage' => $exception->getMessage()
            ]);
        }
    }

    public function generalInfoCustomer(Request $request)
    {
        try {
            $fromDate = $request['fromDate'];
            $toDate = $request['toDate'];
            $realTime = isset($request['realTime']) && $request['realTime'] == 'true' ? true : false;

            $limit = 10;

            $turnByCustomer = $this->getReportDataRepository()->reportTurnByCustomer($fromDate, $toDate, $limit, true, $realTime);

            $dataSource = [
                'customer' => [
                    'labels' => $turnByCustomer['labelCustomerTurns'],
                    'datasets' => $turnByCustomer['dataCustomerTurn'],
                    'total' => $turnByCustomer['totalCustomer'],
                    'extra' => $turnByCustomer['extraCustomer'],
                    'extraPer' => $turnByCustomer['extraCustomerPer'],
                    'extraPerType' => $turnByCustomer['extraCustomerPerType']
                ]
            ];

            return json_encode((object)[
                'errorCode' => HttpCode::EC_OK,
                'errorMessage' => '',
                'data' => $dataSource
            ], JSON_NUMERIC_CHECK);
        } catch (Exception $exception) {
            logError($exception);
            return response()->json([
                'errorCode' => HttpCode::EC_APPLICATION_ERROR,
                'errorMessage' => $exception->getMessage()
            ]);
        }
    }

    public function generalInfoRevenue(Request $request)
    {
        try {
            $fromDate = $request['fromDate'];
            $toDate = $request['toDate'];
            $realTime = isset($request['realTime']) && $request['realTime'] == 'true' ? true : false;

            if (isset($request['dayCondition'])) {
                $dayCondition = $request['dayCondition'];
            } else {
                $dayCondition = env('DAY_CONDITION_DEFAULT', 4);
            }
            $limit = 10;

            $incomeByCustomer = $this->getReportDataRepository()->reportIncomeByCustomer($fromDate, $toDate, $limit, true, $realTime, $dayCondition);
            $topOrderIncome = $this->getReportDataRepository()->getTopOrderIncome($fromDate, $toDate, $dayCondition);

            $revenueLabels = [];
            $revenueDatasets = [];
            foreach ($topOrderIncome as $item) {
                $revenueLabels[] = $item->order_code;
                $revenueDatasets[] = $item->amount;
            }
            $dataSource = [
                'revenue' => [
                    'labels' => $revenueLabels,
                    'datasets' => ['data' => $revenueDatasets],
                    'total' => $incomeByCustomer['total'],
                    'extra' => $incomeByCustomer['extraRevenue'],
                    'extraPer' => $incomeByCustomer['extraRevenuePer'],
                    'extraPerType' => $incomeByCustomer['extraRevenuePerType']
                ]
            ];

            return json_encode((object)[
                'errorCode' => HttpCode::EC_OK,
                'errorMessage' => '',
                'data' => $dataSource
            ], JSON_NUMERIC_CHECK);
        } catch (Exception $exception) {
            logError($exception);
            return response()->json([
                'errorCode' => HttpCode::EC_APPLICATION_ERROR,
                'errorMessage' => $exception->getMessage()
            ]);
        }
    }

    public function generalInfoDocument(Request $request)
    {
        try {
            $fromDate = '';
            $toDate = '';
            if ($request['type'] == 'week') {
                $fromDate = date('Y-m-d', strtotime('last monday'));
                $toDate = date('Y-m-d', strtotime('next sunday'));
            } else if ($request['type'] == 'month') {
                $fromDate = date('Y-m-d', strtotime('first day of this month'));
                $toDate = date('Y-m-d', strtotime('last day of this month'));
            }
            if (isset($request['dayCondition'])) {
                $dayCondition = $request['dayCondition'];
            } else {
                $dayCondition = env('DAY_CONDITION_DEFAULT', 4);
            }
            $customerIds = null;
            if (!empty($request['customerIds'])) {
                $customerIds = explode(";", $request['customerIds']);
            }
            $totalDocument = $this->getReportDataRepository()->reportByDocument($fromDate, $toDate, $dayCondition, $customerIds);

            $dataSource = [
                'document' => [
                    'total' => $totalDocument && count($totalDocument) > 0 ? $totalDocument[0]->total : 0,
                    'status' => $totalDocument && count($totalDocument) > 0 ? $totalDocument[0] : []
                ]
            ];

            return json_encode((object)[
                'errorCode' => HttpCode::EC_OK,
                'errorMessage' => '',
                'data' => $dataSource
            ], JSON_NUMERIC_CHECK);
        } catch (Exception $exception) {
            logError($exception);
            return response()->json([
                'errorCode' => HttpCode::EC_APPLICATION_ERROR,
                'errorMessage' => $exception->getMessage()
            ]);
        }
    }

    public function report(Request $request)
    {
        try {

            $fromDate = $request['fromDate'];
            $toDate = $request['toDate'];
            $type = $request['type'];
            if (isset($request['dayCondition'])) {
                $dayCondition = $request['dayCondition'];
            } else {
                $dayCondition = env('DAY_CONDITION_DEFAULT', 4);
            }
            $dataSource = "";
            switch ($type) {
                case 1:
                    // $dataSource = $this->getReportDataRepository()->reportIncomeByTime($fromDate, $toDate, false, $dayCondition);
                    $dataSource = $this->getReportDataRepository()->reportIncomeCostProfitByTime($fromDate, $toDate, $dayCondition);
                    break;
                case 2:
                    $dataSource = $this->getReportDataRepository()->reportIncomeByCustomer($fromDate, $toDate, 10, false, true, $dayCondition);
                    break;
                case 3:
                    $dataSource = $this->getReportDataRepository()->reportTurnByTime($fromDate, $toDate);
                    break;
                case 4:
                    $dataSource = $this->getReportDataRepository()->reportTurnByCustomer($fromDate, $toDate);
                    break;
                case 5:
                    $dataSource = $this->getReportDataRepository()->reportGoodsByTime($fromDate, $toDate, $dayCondition);
                    break;
            }


            return json_encode((object)[
                'errorCode' => HttpCode::EC_OK,
                'errorMessage' => '',
                'data' => $dataSource
            ], JSON_NUMERIC_CHECK);
        } catch (Exception $exception) {
            logError($exception);
            return response()->json([
                'errorCode' => HttpCode::EC_APPLICATION_ERROR,
                'errorMessage' => $exception->getMessage()
            ]);
        }
    }
}
