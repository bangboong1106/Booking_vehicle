<?php

namespace App\Http\Controllers\Api;

use App\Common\HttpCode;
use App\Http\Controllers\Base\ApiController;
use App\Repositories\CustomerRepository;
use App\Repositories\DriverRepository;
use App\Repositories\FcmTokenRepository;
use App\Repositories\NotificationLogDriverRepository;
use App\Repositories\Driver\OrderDriverRepository;
use App\Repositories\ReportDataRepository;
use Illuminate\Http\Request;
use JWTAuth;
use Mockery\Exception;
use Validator;

class DBAppController extends ApiController
{
    public $loginAfterSignUp = true;
    protected $driverRepos;
    protected $customerRepos;
    protected $notificationLogDriverRepos;
    protected $fcmTokenRepos;
    protected $orderRepos;

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

    public function getDriverRepos()
    {
        return $this->driverRepos;
    }

    public function setDriverRepos($driverRepos)
    {
        $this->driverRepos = $driverRepos;
    }

    public function getFcmTokenRepos()
    {
        return $this->fcmTokenRepos;
    }

    public function setFcmTokenRepos($fcmTokenRepos)
    {
        $this->fcmTokenRepos = $fcmTokenRepos;
    }

    public function getNotificationLogDriverRepos()
    {
        return $this->notificationLogDriverRepos;
    }

    public function setNotificationLogDriverRepos($notificationLogDriverRepos)
    {
        $this->notificationLogDriverRepos = $notificationLogDriverRepos;
    }

    public function getCustomerRepos()
    {
        return $this->customerRepos;
    }

    public function setCustomerRepos($customerRepos)
    {
        $this->customerRepos = $customerRepos;
    }

    public function getOrderRepository()
    {
        return $this->orderRepos;
    }

    public function setOrderRepository($orderRepos)
    {
        $this->orderRepos = $orderRepos;
    }

    public function __construct(
        DriverRepository $driverRepository,
        FcmTokenRepository $fcmTokenRepository,
        NotificationLogDriverRepository $notificationLogDriverRepository,
        CustomerRepository $customerRepository,
        ReportDataRepository $reportDataRepository,
        OrderDriverRepository $orderRepos
    ) {
        parent::__construct();
        $this->setDriverRepos($driverRepository);
        $this->setFcmTokenRepos($fcmTokenRepository);
        $this->setNotificationLogDriverRepos($notificationLogDriverRepository);
        $this->setCustomerRepos($customerRepository);
        $this->setReportDataRepository($reportDataRepository);
        $this->setOrderRepository($orderRepos);
    }

    public function getDriverList(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'pageSize' => 'required',
                'pageIndex' => 'required',
                'textSearch' => '',
                'sort' => [
                    'sortField' => '',
                    'sortType' => ''
                ]
            ]);
            if ($validation->fails()) {
                return response()->json([
                    'errorCode' => HttpCode::EC_BAD_REQUEST,
                    'errorMessage' => $validation->messages()
                ]);
            } else {
                $drivers = $this->getDriverRepos()->getDriverList($request);
                return response()->json([
                    'errorCode' => HttpCode::EC_OK,
                    'errorMessage' => '',
                    'data' => $drivers
                ]);
            }
        } catch (Exception $exception) {
            logError($exception);
            return response()->json([
                'errorCode' => HttpCode::EC_APPLICATION_ERROR,
                'errorMessage' => HttpCode::getMessageForCode(HttpCode::EC_APPLICATION_ERROR)
            ]);
        }
    }

    public function getCustomerList(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'pageSize' => 'required',
                'pageIndex' => 'required',
                'textSearch' => '',
                'sort' => [
                    'sortField' => '',
                    'sortType' => ''
                ]
            ]);
            if ($validation->fails()) {
                return response()->json([
                    'errorCode' => HttpCode::EC_BAD_REQUEST,
                    'errorMessage' => $validation->messages()
                ]);
            } else {
                $drivers = $this->getCustomerRepos()->getCustomerList($request);
                return response()->json([
                    'errorCode' => HttpCode::EC_OK,
                    'errorMessage' => '',
                    'data' => $drivers
                ]);
            }
        } catch (Exception $exception) {
            logError($exception);
            return response()->json([
                'errorCode' => HttpCode::EC_APPLICATION_ERROR,
                'errorMessage' => HttpCode::getMessageForCode(HttpCode::EC_APPLICATION_ERROR)
            ]);
        }
    }

    public function getDashboardData(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'fromDate' => 'required',
                'toDate' => 'required'
            ]);
            if ($validation->fails()) {
                return response()->json([
                    'errorCode' => HttpCode::EC_BAD_REQUEST,
                    'errorMessage' => $validation->messages()
                ]);
            } else {
                $fromDate = $request['fromDate'];
                $toDate = $request['toDate'];
                if (isset($request['dayCondition'])) {
                    $dayCondition = $request['dayCondition'];
                } else {
                    $dayCondition = env('DAY_CONDITION_DEFAULT', 4);
                }
                // TODO: Output
                // Doanh thu theo th???i gian (ng??y ch???n)
                $dataSourceRevenue = $this->getReportDataRepository()->reportIncomeByTime($fromDate, $toDate, false, $dayCondition);
                /* $dataSourceRevenue = [
                     'labels' => ['01', '02', '03', '04', '05', '06', '07'], // Danh s??ch c??c ng??y t??nh doanh thu (from - to date)
                     'datasets' => [
                         'data' => [ // Doanh thu theo ng??y (from - to date) (????n v???: tri???u ?????ng)
                             '3.3', '2.1', '6.4', '1.4', '8.6', '6.5', '3.4'
                         ]
                     ],
                     'extraRevenue' => '1200000', // T???ng Ch??nh l???ch doanh thu
                     'extraRevenuePer' => '11.36', // T???ng Ch??ch l???ch doanh thu t??nh theo %
                     'extraRevenuePerType' => 1 // Lo???i T???ng Doanh thu t??ng l?? 1, gi???m l?? -1
                 ];*/

                $dataSourceOrderCus = $this->getReportDataRepository()->reportTurnByCustomer($fromDate, $toDate, 20, true);
                /*  $dataSourceOrderCus = [
                      'labels' => ['Yamaha', 'Honda', 'Nestle', 'Loan', 'Sony', 'Lazada', 'Shopee'], // Danh s??ch c??c c??ng ty kh??ch h??ng
                      'datasets' => [
                          'data' => [
                              '43', '29', '20', '5', '14', '2', '33' // S??? l?????ng ????n h??ng theo kh??ch h??ng
                          ]
                      ],
                      'extraOrder' => '15', // T???ng Ch??nh l???ch s??? l?????ng ????n h??ng
                      'extraOrderPer' => '18.75', // T???ng Ch??ch l???ch t??? l??? ????n h??ng t??nh theo %
                      'extraOrderPerType' => 1 // Lo???i t???ng ch??ch l???ch ????n h??ng t??ng l?? 1, gi???m l?? -1
                  ];*/
                $dataSourceRevenueCus = $this->getReportDataRepository()->reportIncomeByCustomer($fromDate, $toDate, 20, true, true, $dayCondition);
                /* $dataSourceRevenueCus = [
                     'labels' => ['Yamaha', 'Honda', 'Nestle', 'Loan', 'Sony', 'Lazada', 'Shopee'], // Danh s??ch c??c c??ng ty kh??ch h??ng
                     'datasets' => [
                         'data' => [
                             '3.1', '2.6', '1.2', '0.5', '2.3', '3.3', '0.8' // T???ng doanh thu t??nh theo kh??ch h??ng (????n v??? tri???u ?????ng)
                         ]
                     ],
                     'extraCustomer' => '15', // Ch??nh l???ch s??? l?????ng ????n h??ng
                     'extraCustomerPer' => '18.75', // Ch??ch l???ch t??? l??? ????n h??ng t??nh theo %
                     'extraCustomerPerType' => -1 // Lo???i ch??ch l???ch ????n h??ng t??ng l?? 1, gi???m l?? -1
                 ];*/
                $totalCustomers = $this->getReportDataRepository()->reportByCustomer($fromDate, $toDate);
                $dataSourceRevenueCus['extraCustomer'] = $totalCustomers['extra'];
                $dataSourceRevenueCus['extraCustomerPer'] = $totalCustomers['extraPer'];
                $dataSourceRevenueCus['extraCustomerPerType'] = $totalCustomers['extraPerType'];

                $dataSourceOrderTime = $this->getReportDataRepository()->reportTurnByTime($fromDate, $toDate);

                return response()->json([
                    'errorCode' => HttpCode::EC_OK,
                    'errorMessage' => '',
                    'data' => [
                        'dataSourceRevenue' => $dataSourceRevenue,
                        'dataSourceOrderCus' => $dataSourceOrderCus,
                        'dataSourceOrderTime' => $dataSourceOrderTime,
                        'dataSourceRevenueCus' => $dataSourceRevenueCus,
                    ]
                ]);
            }
        } catch (Exception $exception) {
            logError($exception);
            return response()->json([
                'errorCode' => HttpCode::EC_APPLICATION_ERROR,
                'errorMessage' => HttpCode::getMessageForCode(HttpCode::EC_APPLICATION_ERROR)
            ]);
        }
    }

    public function getOrderCountByToday(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), []);
            if ($validation->fails()) {
                return response()->json([
                    'errorCode' => HttpCode::EC_BAD_REQUEST,
                    'errorMessage' => $validation->messages()
                ]);
            } else {
                $orderData = $this->getOrderRepository()->countOrderStatusByToday();
                return response()->json([
                    'errorCode' => HttpCode::EC_OK,
                    'errorMessage' => '',
                    'data' => $orderData
                ]);
            }
        } catch (Exception $exception) {
            logError($exception);
            return response()->json([
                'errorCode' => HttpCode::EC_APPLICATION_ERROR,
                'errorMessage' => HttpCode::getMessageForCode(HttpCode::EC_APPLICATION_ERROR)
            ]);
        }
    }
}
