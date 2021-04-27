<?php

namespace App\Http\Controllers\Api;

use App\Common\AppConstant;
use App\Common\HttpCode;
use App\Http\Controllers\Base\ApiController;
use App\Model\Entities\DistanceDailyReport;
use App\Model\Entities\GpsSyncLog;
use App\Model\Entities\VehicleDailyReport;
use App\Repositories\CustomerRepository;
use App\Repositories\DistanceDailyReportRepository;
use App\Repositories\DriverRepository;
use App\Repositories\FileRepository;
use App\Repositories\LocationRepository;
use App\Repositories\OrderCustomerRepository;
use App\Repositories\OrderFileRepository;
use App\Repositories\OrderHistoryRepository;
use App\Repositories\Driver\OrderDriverRepository;
use App\Repositories\ReceiptPaymentRepository;
use App\Repositories\RouteCostRepository;
use App\Repositories\RouteFileRepository;
use App\Repositories\Driver\RoutesDriverRepository;
use App\Repositories\TPActionSyncRepository;
use App\Repositories\VehicleRepository;
use App\Repositories\SystemConfigRepository;

use App\Repositories\VinhHienGPSRepository;
use App\Services\NotificationService;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use JWTAuth;
use Mockery\Exception;
use SoapClient;
use SoapHeader;
use Validator;
use PDF;
use App\Model\Entities\File;

class RouteApiController extends ApiController
{

    protected $orderRepos;
    protected $fileRepos;
    protected $orderFileRepos;
    protected $driverRepos;
    protected $orderHistoryRepos;
    protected $customerRepos;
    protected $vehicleRepos;
    protected $routeRepos;
    protected $routeCostRepos;
    protected $routeFileRepos;
    protected $receiptPaymentRepos;
    protected $distanceDailyReportRepository;
    protected $orderCustomerRepos;
    protected $tpActionSyncRepos;
    protected $locationRepos;
    protected $_systemConfigRepository;
    protected $vinhhienGpsRepos;
    protected $_notificationService;

    public function getOrderRepository()
    {
        return $this->orderRepos;
    }

    public function setOrderRepository($orderRepos)
    {
        $this->orderRepos = $orderRepos;
    }

    public function getFileRepos()
    {
        return $this->fileRepos;
    }

    public function setFileRepos($fileRepos)
    {
        $this->fileRepos = $fileRepos;
    }

    public function getOrderFileRepos()
    {
        return $this->orderFileRepos;
    }

    public function setOrderFileRepos($orderFileRepos)
    {
        $this->orderFileRepos = $orderFileRepos;
    }

    public function getDriverRepos()
    {
        return $this->driverRepos;
    }

    public function setDriverRepos($driverRepos)
    {
        $this->driverRepos = $driverRepos;
    }

    public function getOrderHistoryRepos()
    {
        return $this->orderHistoryRepos;
    }

    public function setOrderHistoryRepos($orderHistoryRepos)
    {
        $this->orderHistoryRepos = $orderHistoryRepos;
    }

    public function getCustomerRepos()
    {
        return $this->customerRepos;
    }

    public function setCustomerRepos($customerRepos)
    {
        $this->customerRepos = $customerRepos;
    }

    public function getVehicleRepos()
    {
        return $this->vehicleRepos;
    }

    public function setVehicleRepos($vehicleRepos)
    {
        $this->vehicleRepos = $vehicleRepos;
    }

    public function getRouteRepos()
    {
        return $this->routeRepos;
    }

    public function setRouteRepos($routeRepos)
    {
        $this->routeRepos = $routeRepos;
    }

    public function getRouteCostRepos()
    {
        return $this->routeCostRepos;
    }

    public function setRouteCostRepos($routeCostRepos)
    {
        $this->routeCostRepos = $routeCostRepos;
    }

    public function getRouteFileRepos()
    {
        return $this->routeFileRepos;
    }

    public function setRouteFileRepos($routeFileRepos)
    {
        $this->routeFileRepos = $routeFileRepos;
    }

    public function getReceiptPaymentRepos()
    {
        return $this->receiptPaymentRepos;
    }

    public function setReceiptPaymentRepos($receiptPaymentRepos)
    {
        $this->receiptPaymentRepos = $receiptPaymentRepos;
    }

    /**
     * @return DistanceDailyReportRepository
     */
    public function getDistanceDailyReportRepository()
    {
        return $this->distanceDailyReportRepository;
    }

    /**
     * @param mixed $distanceDailyReportRepository
     */
    public function setDistanceDailyReportRepository($distanceDailyReportRepository): void
    {
        $this->distanceDailyReportRepository = $distanceDailyReportRepository;
    }

    public function getOrderCustomerRepos()
    {
        return $this->orderCustomerRepos;
    }

    public function setOrderCustomerRepos($orderCustomerRepos)
    {
        $this->orderCustomerRepos = $orderCustomerRepos;
    }

    /**
     * @return TPActionSyncRepository
     */
    public function getTPActionSyncRepos()
    {
        return $this->tpActionSyncRepos;
    }

    /**
     * @param mixed $tpActionSyncRepository
     */
    public function setTPActionSyncRepos($tpActionSyncRepository)
    {
        $this->tpActionSyncRepos = $tpActionSyncRepository;
    }

    /**
     * @return mixed
     */
    public function getLocationRepos()
    {
        return $this->locationRepos;
    }

    /**
     * @param mixed $locationRepos
     */
    public function setLocationRepos($locationRepos): void
    {
        $this->locationRepos = $locationRepos;
    }

    /**
     * @return mixed
     */
    public function getSystemConfigRepository()
    {
        return $this->_systemConfigRepository;
    }

    /**
     * @param mixed $orderPaymentRepos
     */
    public function setSystemConfigRepository($systemConfigRepository): void
    {
        $this->_systemConfigRepository = $systemConfigRepository;
    }

    /**
     * @return mixed
     */
    public function getVinhhienGpsRepos()
    {
        return $this->vinhhienGpsRepos;
    }

    /**
     * @param mixed $vinhhienGpsRepos
     */
    public function setVinhhienGpsRepos($vinhhienGpsRepos)
    {
        $this->vinhhienGpsRepos = $vinhhienGpsRepos;
    }

    /**
     * @return mixed
     */
    public function getNotificationService()
    {
        return $this->_notificationService;
    }

    /**
     * @param mixed $notificationService
     */
    public function setNotificationService($notificationService): void
    {
        $this->_notificationService = $notificationService;
    }

    public function __construct(
        OrderDriverRepository $orderRepository,
        FileRepository $fileRepository,
        OrderFileRepository $orderFileRepository,
        DriverRepository $driverRepository,
        OrderHistoryRepository $orderHistoryRepository,
        CustomerRepository $customerRepository,
        VehicleRepository $vehicleRepos,
        RoutesDriverRepository $routesRepository,
        RouteCostRepository $routeCostRepos,
        RouteFileRepository $routeFileRepository,
        ReceiptPaymentRepository $receiptPaymentRepository,
        DistanceDailyReportRepository $distanceDailyReportRepository,
        OrderCustomerRepository $orderCustomerRepository,
        TPActionSyncRepository $tpActionSyncRepository,
        LocationRepository $locationRepository,
        SystemConfigRepository $systemConfigRepository,
        VinhHienGPSRepository $vinhHienGPSRepository,
        NotificationService $notificationService
    )
    {
        parent::__construct();
        $this->setOrderRepository($orderRepository);
        $this->setFileRepos($fileRepository);
        $this->setOrderFileRepos($orderFileRepository);
        $this->setDriverRepos($driverRepository);
        $this->setOrderHistoryRepos($orderHistoryRepository);
        $this->setCustomerRepos($customerRepository);
        $this->setVehicleRepos($vehicleRepos);
        $this->setRouteRepos($routesRepository);
        $this->setRouteCostRepos($routeCostRepos);
        $this->setRouteFileRepos($routeFileRepository);
        $this->setReceiptPaymentRepos($receiptPaymentRepository);
        $this->setDistanceDailyReportRepository($distanceDailyReportRepository);
        $this->setOrderCustomerRepos($orderCustomerRepository);
        $this->setTPActionSyncRepos($tpActionSyncRepository);
        $this->setLocationRepos($locationRepository);
        $this->setSystemConfigRepository($systemConfigRepository);
        $this->setRepository($routesRepository);
        $this->setVinhhienGpsRepos($vinhHienGPSRepository);
        $this->setNotificationService($notificationService);
    }

    public function getRoutes(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'pageSize' => 'required',
                'pageIndex' => 'required',
                'status' => 'required',
                'textSearch' => '',
                'fromDate' => '',
                'toDate' => '',
                'isApproved' => '',
                'filter' => [
                    'filterType' => '',
                    'filterField' => '',
                    'textSearch' => ''
                ],
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
                $request['pageSize'] = 100;

                $userId = Auth::User()->id;
                $driverObj = $this->getDriverRepos()->getFullInfoDriverWithUserId($userId);
                $routes = null;
                if (isset($driverObj)) {
                    $routes = $this->getRouteRepos()->getRoutesByDriverIdAndStatus($driverObj->id, $request);
                }

                return response()->json([
                    'errorCode' => HttpCode::EC_OK,
                    'errorMessage' => '',
                    'data' => $routes
                ]);
            }
        } catch (Exception $exception) {
            return response()->json([
                'errorCode' => HttpCode::EC_APPLICATION_ERROR,
                'errorMessage' => HttpCode::getMessageForCode(HttpCode::EC_APPLICATION_ERROR)
            ]);
        }
    }

    public function getRouteDetail(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'id' => 'required',
            ]);
            if ($validation->fails()) {
                return response()->json([
                    'errorCode' => HttpCode::EC_BAD_REQUEST,
                    'errorMessage' => $validation->messages()
                ]);
            } else {
                $route = $this->getRouteRepos()->getItemById($request['id']);
                $item = $this->getRouteRepos()->getTotalOrdersOnRoute($request['id']);
                if ($item != null) {
                    $route->total = $item->total;
                    $route->total_complete = $item->total_complete;
                }
                return response()->json([
                    'errorCode' => HttpCode::EC_OK,
                    'errorMessage' => '',
                    'data' => $route
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

    public function getOrdersByRouteID(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'id' => 'required',
            ]);
            if ($validation->fails()) {
                return response()->json([
                    'errorCode' => HttpCode::EC_BAD_REQUEST,
                    'errorMessage' => $validation->messages()
                ]);
            }
            $routeId = $request->get('id', '');
            $request['pageSize'] = 100;
            $orders = empty($routeId) ? [] : $this->getOrderRepository()->getItemsByRouteId($routeId);

            $items = $this->getOrderRepository()->getTotalOrdersOnRoute($orders->pluck('id')->toArray());
            foreach ($orders as $order) {
                $tmp = collect($items)->filter(function ($item) use ($order) {
                    return $item->id === $order->order_id;
                })->first();
                if ($tmp != null) {
                    $order->total_delivery = $tmp->total_delivery;
                    $order->total_arrival = $tmp->total_arrival;
                }
            }

            return response()->json([
                'errorCode' => HttpCode::EC_OK,
                'errorMessage' => '',
                'data' => $orders
            ]);
        } catch (Exception $exception) {
            logError($exception);
            return response()->json([
                'errorCode' => HttpCode::EC_APPLICATION_ERROR,
                'errorMessage' => $exception->getMessage()
            ]);
        }
    }

    public function getLocationByOrderId(Request $request)
    {

        try {
            $validation = Validator::make($request->all(), [
                'order_id' => 'required'
            ]);
            if ($validation->fails()) {
                return response()->json([
                    'errorCode' => HttpCode::EC_BAD_REQUEST,
                    'errorMessage' => $validation->messages()
                ]);
            } else {
                $orderId = $request->get('order_id', '');
                $locations = $this->getOrderRepository()->getLocationItemsByOrderID($orderId);

                return response()->json([
                    'errorCode' => HttpCode::EC_OK,
                    'errorMessage' => '',
                    'data' => $locations
                ]);
            }
        } catch (Exception $exception) {
            return response()->json([
                'errorCode' => HttpCode::EC_APPLICATION_ERROR,
                'errorMessage' => HttpCode::getMessageForCode(HttpCode::EC_APPLICATION_ERROR)
            ]);
        }
    }

    public function getGoodsByOrderId(Request $request)
    {

        try {
            $validation = Validator::make($request->all(), [
                'id' => 'required'
            ]);
            if ($validation->fails()) {
                return response()->json([
                    'errorCode' => HttpCode::EC_BAD_REQUEST,
                    'errorMessage' => $validation->messages()
                ]);
            } else {
                $orderId = $request->get('id', '');
                $goods = $this->getOrderRepository()->getGoodsItemsByOrderID($orderId);

                return response()->json([
                    'errorCode' => HttpCode::EC_OK,
                    'errorMessage' => '',
                    'data' => $goods
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

    public function getFilesRouteByRouteId(Request $request)
    {

        try {
            $validation = Validator::make($request->all(), [
                'id' => 'required'
            ]);
            if ($validation->fails()) {
                return response()->json([
                    'errorCode' => HttpCode::EC_BAD_REQUEST,
                    'errorMessage' => $validation->messages()
                ]);
            } else {
                $routeId = $request->get('id', '');

                $routeFiles = $this->getRouteFileRepos()->getRouteFileWithRouteID($routeId, config('constant.ROUTE_FILE_TYPE_GENERAL', '0'));
                $data = $this->doProcessFileRoute($routeFiles);
                return response()->json([
                    'errorCode' => HttpCode::EC_OK,
                    'errorMessage' => '',
                    'data' => $data
                ]);
            }
        } catch (Exception $exception) {
            return response()->json([
                'errorCode' => HttpCode::EC_APPLICATION_ERROR,
                'errorMessage' => HttpCode::getMessageForCode(HttpCode::EC_APPLICATION_ERROR)
            ]);
        }
    }

    public function doProcessFileRoute($routeFiles)
    {
        $attachments = [];
        $attachment = null;
        if ($routeFiles != null) {
            $files = null;
            foreach ($routeFiles as $key => $route_file) {
                $attachment['note'] = $route_file->note;
                $file = null;
                if ($route_file->file_id != null) {
                    $fileInfo = app('App\Http\Controllers\Api\FileApiController')->getFile($route_file->file_id);
                    if ($fileInfo != null) {
                        $file['urlThumbnail'] = str_replace('\\', '/', $fileInfo['thumbnail']);
                        $file['url'] = str_replace('\\', '/', $fileInfo['url']);
                        $file['type'] = $fileInfo['type'];
                        $file['name'] = $fileInfo['name'];
                        $file['insDate'] = $fileInfo['insDate'];
                        $file['fileId'] = $route_file->file_id;
                    }
                }
                if ($file != null)
                    $files[] = $file;
            }
            if ($files != null && !empty($files))
                $attachment['files'] = $files;
        }
        if ($attachment != null && !empty($attachment))
            $attachments[] = $attachment;
        $data = [
            'attachments' => $attachments
        ];
        return $data;
    }

    public function updateRoutes(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'route_id' => 'required',
                'order_status' => 'required',
            ]);
            if ($validation->fails()) {
                return response()->json([
                    'errorCode' => HttpCode::EC_BAD_REQUEST,
                    'errorMessage' => $validation->messages()
                ]);
            } else {

                DB::beginTransaction();

                $data = $request->json()->all();
                $routeId = $data['route_id'];
                $orderStatus = $data['order_status'];
                $costs = $data['costs'];
                $files = $data['files'];
                $note = $data['note'];

                switch ($orderStatus) {
                    case config('constant.SAN_SANG'): //Từ chối
                        if ($this->getRouteRepos()->checkConditionCancelRoute($routeId)) {
                            $this->_processRouteFromDriver(2, $routeId, $costs, $files, $note, $orderStatus);
                        } else {
                            return response()->json([
                                'errorCode' => HttpCode::EC_APPLICATION_WARNING,
                                'errorMessage' => 'Chuyến đã có đơn hoàn thành . Bạn không thể hủy chuyến',
                                'data' => [
                                    'message' => 'ok'
                                ]
                            ]);
                        }
                        break;
                    case config('constant.CHO_NHAN_HANG'): //Chấp nhận chuyến
                        break;
                    case config('constant.DANG_VAN_CHUYEN'): //Nhận hàng
                    case config('constant.HOAN_THANH'): //Trả hàng
                        $time = 0;
                        $config = $this->getSystemConfigRepository()->search(['key_eq' => 'DriverMobile.CompletedLimitTime'])->get();

                        if (!$config->isEmpty()) {
                            $time = $config->first()->value == null ? 0 : $config->first()->value;
                        }
                        if ($time != 0) {
                            if ($this->getRouteRepos()->checkConditionCompleteRoute($routeId, $time)) {
                                $this->_processRouteFromDriver(1, $routeId, $costs, $files, $note, $orderStatus);
                            } else {
                                return response()->json([
                                    'errorCode' => HttpCode::EC_APPLICATION_WARNING,
                                    'errorMessage' => 'Chuyến xe có đơn chưa đến thời gian trả hàng. Bạn không thể hoàn thành chuyến',
                                    'data' => [
                                        'message' => 'ok'
                                    ]
                                ]);
                            }
                        } else {
                            $this->_processRouteFromDriver(1, $routeId, $costs, $files, $note, $orderStatus);
                        }
                        break;
                }

                DB::commit();

                return response()->json([
                    'errorCode' => HttpCode::EC_OK,
                    'errorMessage' => '',
                    'data' => [
                        'message' => 'ok'
                    ]
                ]);
            }
        } catch (Exception $exception) {
            DB::rollBack();
            logError($exception . '-' . json_encode($request));
            return response()->json([
                'errorCode' => HttpCode::EC_APPLICATION_ERROR,
                'errorMessage' => HttpCode::getMessageForCode(HttpCode::EC_APPLICATION_ERROR)
            ]);
        }
    }

    public function updateRouteFiles(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'fileIds' => 'required',
                'routeId' => 'required',
                'type' => 'required', // Xoa hoac them moi tat ca
                'actionType' => ''
            ]);
            if ($validation->fails()) {
                return response()->json([
                    'errorCode' => HttpCode::EC_BAD_REQUEST,
                    'errorMessage' => $validation->messages()
                ]);
            } else {
                DB::beginTransaction();

                $fileIds = $request->get('fileIds', []);
                $routeId = $request->get('routeId', '');
                $type = $request->get('type', config('constant.ROUTE_FILE_TYPE_GENERAL')); // 0: general - 1: cost
                $actionType = $request->get('actionType', '0'); // 0: insert - 1: delete
                $costId = $request->get('costId', ''); // 0: insert - 1: delete
                if (0 == $actionType) {
                    foreach ($fileIds as $fileId) {
                        $routeFile = $this->getRouteFileRepos()->findFirstOrNew([]);
                        $routeFile->route_id = $routeId;
                        $routeFile->file_id = $fileId;
                        $routeFile->type = $type;
                        $routeFile->cost_id = $costId;
                        $routeFile->save();
                    }
                } else if (1 == $actionType) {
                    foreach ($fileIds as $fileId) {
                        $routeFile = $this->getRouteFileRepos()->getRouteFileWithFileIdAndRouteId($fileId, $routeId, $type);
                        if ($routeFile != null) {
                            $routeFile->delete();
                        }
                    }
                }
                $routeFiles = $this->getRouteFileRepos()->getRouteFileWithRouteID($routeId, $type);
                $data = $this->doProcessFileRoute($routeFiles);

                DB::commit();

                return response()->json([
                    'errorCode' => HttpCode::EC_OK,
                    'errorMessage' => '',
                    'data' => $data
                ]);
            }
        } catch (Exception $exception) {
            DB::rollBack();
            logError($exception . '-' . json_encode($request));
            return response()->json([
                'errorCode' => HttpCode::EC_APPLICATION_ERROR,
                'errorMessage' => HttpCode::getMessageForCode(HttpCode::EC_APPLICATION_ERROR)
            ]);
        }
    }

    public function updateCostRoute(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'routeId' => 'required',
                'costs' => 'required'
            ]);
            if ($validation->fails()) {
                return response()->json([
                    'errorCode' => HttpCode::EC_BAD_REQUEST,
                    'errorMessage' => $validation->messages()
                ]);
            } else {
                $routeId = $request->get('routeId', '');
                $costs = $request->get('costs', []);
                if ($costs) {
                    foreach ($costs as $cost) {
                        if (isset($cost['receipt_payment_id']) && isset($cost['amount'])) {
                            $routeCostEntity = $this->getRouteCostRepos()->getCost($routeId, $cost['receipt_payment_id']);
                            if ($routeCostEntity == null) {
                                $routeCostEntity = $this->getRouteCostRepos()->findFirstOrNew([]);
                            }

                            $routeCostEntity->route_id = $routeId;
                            $routeCostEntity->receipt_payment_id = $cost['receipt_payment_id'];
                            $routeCostEntity->amount_driver = (float)$cost['amount'];
                            $routeCostEntity->description = isset($cost['description']) ? $cost['description'] : null;
                            $routeCostEntity->save();
                        }
                    }

                    //Thông báo cập nhật chi phí từ TX
                    $this->getNotificationService()->notifyDriverToC20AndPartner(2, ['route_id' => $routeId]);
                }
                return response()->json([
                    'errorCode' => HttpCode::EC_OK,
                    'errorMessage' => '',
                    'data' => [
                        'message' => 'ok'
                    ]
                ]);
            }
        } catch (Exception $exception) {
            logError($exception . '-' . json_encode($request));
            return response()->json([
                'errorCode' => HttpCode::EC_APPLICATION_ERROR,
                'errorMessage' => $exception->getMessage()
            ]);
        }
    }

    public function getCostRoute(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), []);
            if ($validation->fails()) {
                return response()->json([
                    'errorCode' => HttpCode::EC_BAD_REQUEST,
                    'errorMessage' => $validation->messages()
                ]);
            } else {
                $routeId = $request->get('id', '');
                $routeCost = $this->getReceiptPaymentRepos()->getAll($routeId);
                $routeFiles = $this->getRouteFileRepos()->getRouteFileWithRouteID($routeId, config('constant.ROUTE_FILE_TYPE_COST', '1'));
                if (!empty($routeCost)) {
                    foreach ($routeCost as &$cost) {
                        $fillArr = [];
                        if (!empty($routeFiles)) {
                            foreach ($routeFiles as $file) {
                                if (isset($file) && !empty($file->file_id) && $file->cost_id == $cost->costId) {
                                    $obj = null;
                                    $fileInfo = app('App\Http\Controllers\Api\FileApiController')->getFile($file->file_id);
                                    if ($fileInfo != null) {
                                        $obj['urlThumbnail'] = str_replace('\\', '/', $fileInfo['thumbnail']);
                                        $obj['url'] = str_replace('\\', '/', $fileInfo['url']);
                                        $obj['type'] = $fileInfo['type'];
                                        $obj['name'] = $fileInfo['name'];
                                        $obj['insDate'] = $fileInfo['insDate'];
                                        $obj['fileId'] = $file->file_id;
                                    }
                                    $fillArr[] = $obj;
                                }
                            }
                        }
                        $cost->files = $fillArr;
                        $cost->default_amount = (empty($cost->default_amount) || $cost->default_amount == 0) ?
                            [] :
                            explode('|', $cost->default_amount);
                    }
                }
                return response()->json([
                    'errorCode' => HttpCode::EC_OK,
                    'errorMessage' => '',
                    'data' => $routeCost
                ]);
            }
        } catch (Exception $exception) {
            logError($exception);
            return response()->json([
                'errorCode' => HttpCode::EC_APPLICATION_ERROR,
                'errorMessage' => $exception->getMessage()
            ]);
        }
    }

    /**
     * @param $action 1-Tài xế cập nhật chuyến , 2- Tài xế từ chối
     * @param $routeId
     * @param $costs
     * @param null $fileIds
     * @param null $note
     * @param $orderStatus
     */
    public function _processRouteFromDriver($action, $routeId, $costs = null, $fileIds = null, $note = null, $orderStatus)
    {
        $route = $this->getRouteRepos()->getItemById($routeId);
        if ($action == 1) {
            //Cập nhật trạng thái, lịch sử, thông báo cho admin cho đơn thuộc chuyến
            $routeOrders = $this->getOrderRepository()->getOrdersByRouteId($routeId);
            $currentDay = new DateTime();
            foreach ($routeOrders as $order) {
                if ($order->status == config('constant.HUY'))
                    continue;

                $orderOld = $order->replicate();

                $dateReality = $currentDay->format('Y-m-d');
                $timeReality = $currentDay->format('H:i');

                if ($orderStatus == config('constant.DANG_VAN_CHUYEN')) {
                    $order->ETD_date_reality = $dateReality;
                    $order->ETD_time_reality = $timeReality;
                    //Cập nhật thời gian nhận hàng cho location đầu tiên ở bảng order location
                    $this->getOrderRepository()->updateDateRealityOrderLocation(
                        $order->id,
                        $order->location_destination_id,
                        $dateReality,
                        $timeReality
                    );
                }
                if ($orderStatus == config('constant.HOAN_THANH')) {
                    if (!isset($order->ETD_date_reality)) {
                        $order->ETD_date_reality = $dateReality;
                        $order->ETD_time_reality = $timeReality;
                        //Cập nhật thời gian nhận hàng cho location đầu tiên ở bảng order location
                        $this->getOrderRepository()->updateDateRealityOrderLocation(
                            $order->id,
                            $order->location_destination_id,
                            $dateReality,
                            $timeReality
                        );
                    }
                    if (!isset($order->ETA_date_reality)) {
                        $order->ETA_date_reality = $dateReality;
                        $order->ETA_time_reality = $timeReality;
                        //Cập nhật thời gian trả hàng cho location đầu tiên ở bảng order location
                        $this->getOrderRepository()->updateDateRealityOrderLocation(
                            $order->id,
                            $order->location_arrival_id,
                            $dateReality,
                            $timeReality
                        );
                    }
                    //Cập nhật hạn thu chứng từ
                    $this->getOrderRepository()->updateDateTimeCollectedDocument($order->id, $dateReality, $timeReality);
                }
                $order->status = $orderStatus;
                $order->save();

                //Trigger tạo bản ghi đồng bộ đối tác
                $this->getTPActionSyncRepos()->triggerActionSync($orderOld, $order);


                // Notify to admin , partner
                $this->getNotificationService()->notifyDriverToC20AndPartner(1, [
                    'order_status' => $orderStatus,
                    'order_id' => $order->id, 'order_code' => $order->order_code, 'customer_id' => $order->customer_id, 'partner_id' => $order->partner_id
                ]);
            }
            //Lưu chi phi tài xế nhập
            if ($costs) {
                foreach ($costs as $cost) {
                    if (isset($cost['receipt_payment_id']) && isset($cost['amount'])) {
                        $routeCostEntity = $this->getRouteCostRepos()->getCost($routeId, $cost['receipt_payment_id']);
                        if ($routeCostEntity == null)
                            $routeCostEntity = $this->getRouteCostRepos()->findFirstOrNew([]);

                        $routeCostEntity->route_id = $routeId;
                        $routeCostEntity->receipt_payment_id = $cost['receipt_payment_id'];
                        $routeCostEntity->amount_driver = (float)$cost['amount'];
                        $routeCostEntity->save();
                    }
                }

                if ($route) {
                    $route->route_note = $note;
                    $route->save();
                }
            }
            //Lưu file tài xế nhập
            if ($fileIds) {
                foreach ($fileIds as $fileId) {
                    $routeFile = $this->getRouteFileRepos()->findFirstOrNew([]);
                    $routeFile->route_id = $routeId;
                    $routeFile->file_id = $fileId;
                    $routeFile->type = config('constant.ROUTE_FILE_TYPE_GENERAL');
                    $routeFile->note = $note;
                    $routeFile->save();
                }
            }
        }

        if ($action == 2) {
            //Xóa xe - tài xế ,cập nhật trạng thái về sẵn sàng, thông báo cho admin của đơn thuộc chuyến
            $routeOrders = $this->getOrderRepository()->getOrdersByRouteId($routeId);
            foreach ($routeOrders as $order) {
                $order->status = config('constant.SAN_SANG');
                $order->vehicle_id = null;
                $order->primary_driver_id = null;
                $order->secondary_driver_id = null;
                $order->route_id = null;
                $order->save();

                // Notify to admin , partner
                $this->getNotificationService()->notifyDriverToC20AndPartner(1, ['order_status' => config('constant.SAN_SANG'),
                    'order_id' => $order->id, 'order_code' => $order->order_code, 'customer_id' => $order->customer_id, 'partner_id' => $order->partner_id]);

            }
        }

        if ($route) {
            $routeOrders = $this->getOrderRepository()->getOrdersByRouteId($route->id);

            //Xóa chuyến nếu chuyến không có đơn hàng
            if ($routeOrders == null || count($routeOrders) == 0) {
                // Nếu tài xế hủy chuyến => chuyển chuyến sang trạng thái hủy, xóa đơn khỏi chuyến, đưa đơn về sẵn sàng.
                if ($action == 2) {
                    $route->route_status = config('constant.status_cancel');
                    $route->save();
                } else {
                    $this->getRouteCostRepos()->deleteWhere(['route_id' => $route->id]);
                    $route->delete();
                }
            } else {
                //Cập nhật lại thông tin của chuyến
                $ETD_date = null;
                $ETD_time = null;
                $ETA_date = null;
                $ETA_time = null;
                $ETD_date_reality = null;
                $ETD_time_reality = null;
                $ETA_date_reality = null;
                $ETA_time_reality = null;
                $location_destination_id = null;
                $location_arrival_id = null;

                $status = config('constant.status_incomplete');
                $orders = $this->getOrderRepository()->getOrdersByRouteId($route->id);

                $countCancel = 0;
                $countComplete = 0;
                foreach ($orders as $order) {
                    if ($order->status == config('constant.HUY'))
                        $countCancel++;
                    if ($order->status == config('constant.HOAN_THANH'))
                        $countComplete++;

                    if ($order->ETD_date != null) {
                        $dateTime1 = $order->ETD_date . ' ' . ($order->ETD_time ? $order->ETD_time : '');
                        $dateTime2 = $ETD_date . ' ' . ($ETD_time ? $ETD_time : '');
                        if ($ETD_date == null || AppConstant::isDate2GreatDate1($dateTime1, $dateTime2)) {
                            $ETD_date = $order->ETD_date;
                            $ETD_time = $order->ETD_time;
                            $location_destination_id = $order->location_destination_id;
                        }
                    }
                    if ($order->ETA_date != null) {
                        $dateTime2 = $order->ETA_date . ' ' . ($order->ETA_time ? $order->ETA_time : '');
                        $dateTime1 = $ETA_date . ' ' . ($ETA_time ? $ETA_time : '');
                        if ($ETA_date == null || AppConstant::isDate2GreatDate1($dateTime1, $dateTime2)) {
                            $ETA_date = $order->ETA_date;
                            $ETA_time = $order->ETA_time;
                            $location_arrival_id = $order->location_arrival_id;
                        }
                    }

                    if ($order->status == config('constant.HOAN_THANH') && $order->ETA_date_reality != null) {
                        $dateTime2 = $order->ETA_date_reality . ' ' . ($order->ETA_time_reality ? $order->ETA_time_reality : '');
                        $dateTime1 = $ETA_date_reality . ' ' . ($ETA_time_reality ? $ETA_time_reality : '');
                        if ($ETA_date_reality == null || AppConstant::isDate2GreatDate1($dateTime1, $dateTime2)) {
                            $ETA_date_reality = $order->ETA_date_reality;
                            $ETA_time_reality = $order->ETA_time_reality;
                        }
                    }
                    if ($order->ETD_date_reality != null) {
                        $dateTime1 = $order->ETD_date_reality . ' ' . ($order->ETD_time_reality ? $order->ETD_time_reality : '');
                        $dateTime2 = $ETD_date_reality . ' ' . ($ETD_time_reality ? $ETD_time_reality : '');
                        if ($ETD_date_reality == null || AppConstant::isDate2GreatDate1($dateTime1, $dateTime2)) {
                            $ETD_date_reality = $order->ETD_date_reality;
                            $ETD_time_reality = $order->ETD_time_reality;
                        }
                    }
                }

                $routeName = '';
                $locationDes = $this->getLocationRepos()->getLocationsById($location_destination_id);
                $routeName .= $locationDes ? $locationDes->title : "";
                $locationArr = $this->getLocationRepos()->getLocationsById($location_arrival_id);
                $routeName .= "-" . ($locationArr ? $locationArr->title : "");

                if (!empty($orders)) {
                    if ($countCancel == count($orders))
                        $status = config('constant.status_cancel');
                    else if (
                        $countComplete == count($orders) ||
                        ($countComplete > 0 && ($countComplete + $countCancel) == count($orders))
                    )
                        $status = config('constant.status_complete');
                }

                $route->name = $routeName;
                $route->ETD_date = $ETD_date;
                $route->ETD_time = $ETD_time;
                $route->ETA_date = $ETA_date;
                $route->ETA_time = $ETA_time;
                $route->location_destination_id = $location_destination_id;
                $route->location_arrival_id = $location_arrival_id;
                if ($status == config('constant.status_complete')) {
                    $route->ETA_date_reality = $ETA_date_reality;
                    $route->ETA_time_reality = $ETA_time_reality;
                }
                if ($ETD_date_reality != null) {
                    $route->ETD_date_reality = $ETD_date_reality;
                    $route->ETD_time_reality = $ETD_time_reality;
                }
                $route->route_status = $status;
                $route->route_note = $note;

                //Lưu thông tin dư thừa trên chuyến
                if ($orders) {
                    $orderArray = is_array($orders) ? $orders : $orders->toArray();
                    $orderCodes = implode(';', array_filter(array_column($orderArray, 'order_code')));
                    $orderNotes = implode('|', array_filter(array_column($orderArray, 'note')));
                    $customerIds = implode(',', array_unique(array_filter(array_column($orderArray, 'customer_id'))));
                    $vinNos = implode(';', array_filter(array_column($orderArray, 'vin_no')));
                    $modelNos = implode(';', array_filter(array_column($orderArray, 'model_no')));
                    $volume = array_sum(array_column($orderArray, 'volume'));
                    $weight = array_sum(array_column($orderArray, 'weight'));
                    $quantity = array_sum(array_column($orderArray, 'quantity'));
                    $totalAmount = array_sum(array_column($orderArray, 'amount'));

                    $route->order_codes = $orderCodes;
                    $route->order_notes = $orderNotes;
                    $route->customer_ids = $customerIds;
                    $route->volume = $volume;
                    $route->weight = $weight;
                    $route->quantity = $quantity;
                    $route->total_amount = $totalAmount;
                    $route->count_order = count($orders);
                    $route->vin_nos = $vinNos;
                    $route->model_nos = $modelNos;
                }

                $route->save();
            }
        }
    }

    public function updateVehicleStatus(Request $request)
    {
        try {
            $this->getVehicleRepos()->updateVehicleStatus();
        } catch (Exception $exception) {
        }
    }

    public function getDailyReportsByVehicle(Request $request)
    {
        try {
            $result = [];
            $gc = $request['gpsCompanyId'];

            if ($gc == config('constant.GC_VIET_MAPS')) {
                $gpsId = $request['gpsId'];
                $from = $request['from'];
                $to = $request['to'];
                $gpsSyncLog = new GpsSyncLog();

                try {
                    $client = new \GuzzleHttp\Client();
                    $request = $client->get('https://client-api.quanlyxe.vn/v3/tracking/GetDailyReports?id=' . $gpsId . '&from=' . $from . '&to=' . $to . '&apikey=' . env('GPS_VIETMAPS_API_KEY'));
                    $response = $request->getBody();
                    $gpsSyncLog->request = json_encode(['request' => 'https://client-api.quanlyxe.vn/v3/tracking/GetDailyReports?id=' . $gpsId . '&from=' . $from . '&to=' . $to . '&apikey=' . env('GPS_VIETMAPS_API_KEY')]);

                    if ($response != null) {
                        $content = $response->getContents();
                        $data = json_decode($content);
                        if (!empty($data)) {
                            $result = $data->Data;
                        }
                    }
                } catch (\Exception $exception) {
                    $gpsSyncLog->error_code = 'Exception';
                    $gpsSyncLog->error_message = $exception->getMessage();
                }
                $gpsSyncLog->type_request = 'GetDailyReports';
                $gpsSyncLog->save();
            } else if ($gc == config('constant.GC_EUPFIN')) {
                $startTime = $request['from'];
                $endTime = $request['to'];
                $vehicleNo = $request['vehicle_plate'];

                $gpsSyncLog = new GpsSyncLog();
                try {
                    $client = new \GuzzleHttp\Client(['headers' => ['X-Eupfin-Api-Key' => env('GPS_EUPFIN_API_KEY')]]);
                    $body['account'] = env('GPS_EUPFIN_ACCOUNT');
                    $body['password'] = env('GPS_EUPFIN_PASSWORD');
                    $body['vehicleNo'] = $vehicleNo;
                    $body['startTime'] = $startTime;
                    $body['endTime'] = $endTime;

                    $request = $client->post(env('GPS_EUPFIN_URL_HISTORY', 'http://api.eup.net.vn:8000/thanhdat/history'), ['form_params' => $body]);
                    $response = $request->getBody();
                    $gpsSyncLog->request = json_encode(['request' => env('GPS_EUPFIN_URL_HISTORY', 'http://api.eup.net.vn:8000/thanhdat/history')]);

                    if ($response != null) {
                        $content = $response->getContents();
                        $data = json_decode($content);
                        if (!empty($data)) {
                            $result = $data;
                        }
                    }
                } catch (\Exception $exception) {
                    $gpsSyncLog->error_code = 'Exception';
                    $gpsSyncLog->error_message = $exception->getMessage();
                }
                $gpsSyncLog->type_request = 'GetDailyReports';
                $gpsSyncLog->save();
            } else if ($gc == config('constant.GC_VCOMSAT')) {
                $startTime = $request['from'];
                $endTime = $request['to'];
                $vehicleNo = $request['vehicle_plate'];

                $gpsSyncLog = new GpsSyncLog();
                try {
                    $client = new SoapClient(env('GPS_VCOMSAT_WEB_SERVICE_WSDL', 'http://stagingws.giamsathanhtrinh.vn/SmartLog.asmx?WSDL'));
                    $auth = array(
                        'Username' => env('GPS_VCOMSAT_USER_HEADER', 'smartlog'),
                        'Password' => env('GPS_VCOMSAT_PASSWORD_HEADER', 'p@ssw0rd'),
                    );
                    $header = new SoapHeader('http://tempuri.org/', 'ServiceAuthHeader', $auth, false);
                    $client->__setSoapHeaders($header);

                    $params = array(
                        'CarPlate' => $vehicleNo,
                        'FromDate' => $startTime,
                        'ToDate' => $endTime,
                    );
                    $gpsSyncLog->request = json_encode(['request' => $params]);
                    $response = $client->__soapCall(env('GPS_VCOMSAT_FUNCTION_HISTORY_NAME', 'GetCarSignalByPlate'), array($params));

                    if (isset($response)) {
                        $getCarInfoResult = $response->GetCarSignalByPlateResult;
                        if (isset($getCarInfoResult)) {
                            $carReviewSignal = $getCarInfoResult->CarReviewSignal;
                            if (isset($carReviewSignal) & sizeof($carReviewSignal) > 0) {
                                $gpsSyncLog->error_message = 'OK';
                                $gpsSyncLog->response = '';
                                $result = $carReviewSignal[sizeof($carReviewSignal) - 1]; // lấy bản ghi cuối cùng
                            } else {
                                $gpsSyncLog->error_message = '$carReviewSignal empty';
                            }
                        } else {
                            $gpsSyncLog->error_message = '$getCarInfoResult empty';
                        }
                    } else {
                        $gpsSyncLog->error_message = '$response empty';
                    }
                } catch (\Exception $exception) {
                    $gpsSyncLog->error_code = 'Exception';
                    $gpsSyncLog->error_message = $exception->getMessage();
                }
                $gpsSyncLog->type_request = 'GetDailyReports';
                $gpsSyncLog->save();
            }

            return response()->json([
                'errorCode' => HttpCode::EC_OK,
                'errorMessage' => '',
                'data' => $result
            ]);
        } catch (Exception $exception) {
            return response()->json([
                'errorCode' => HttpCode::EC_APPLICATION_ERROR,
                'errorMessage' => HttpCode::getMessageForCode(HttpCode::EC_APPLICATION_ERROR)
            ]);
        }
    }

    public function doDailyReportsAll(Request $request)
    {
        $enableGps = env('GPS_STATUS', false);
        if ($enableGps) {
            $GCs = explode(',', env('GPS_COMPANY'));
            foreach ($GCs as $gc) {
                if ($gc == config('constant.GC_VIET_MAPS')) {
                    $this->doDailyVietMaps();
                } else if ($gc == config('constant.GC_ADA')) {
                    $this->doDailyADA();
                } else if ($gc == config('constant.GC_EUPFIN')) {
                    $this->doDailyEupfin();
                } else if ($gc == config('constant.GC_VCOMSAT')) {
                    $this->doDailyVComSat();
                } else if ($gc == config('constant.GC_BINH_ANH_2')) {
                    $this->doDailyBinhAnh2();
                } else if ($gc == config('constant.GC_EPOSI')) {
                    $this->doDailyEposi();
                } else if ($gc == config('constant.GC_ADSUN')) {
                    $this->doDailyAdsun();
                }
            }
        }
        return response()->json([
            'errorCode' => HttpCode::EC_OK,
            'errorMessage' => '',
            'data' => [
                'message' => 'OK'
            ]
        ]);
    }

    public function doDailyAdsun()
    {
        $gpsSyncLog = new GpsSyncLog();
        try {
            $client = new \GuzzleHttp\Client();

            $url = env('GPS_ADSUN_URL_ALL') . env('GPS_ADSUN_COMPANY_ID') . '&username=' . env('GPS_ADSUN_USER') . '&pwd=' . env('GPS_ADSUN_PASSWORD');
            $request = $client->get($url);
            $response = $request->getBody();
            $gpsSyncLog->request = json_encode(['request' => $url]);

            if ($response != null) {
                $content = $response->getContents();
                $gpsSyncLog->response = 'doDailyAdsun OK';
                $data = json_decode($content);
                if (!empty($data)) {
                    foreach ($data->Data as $obj) {
                        $vehicle = $this->getVehicleRepos()->getVehicleByGpsCompanyAndVehiclePlate(config('constant.GC_ADSUN'), $obj->ActualPlate);
                        if (isset($vehicle)) {
                            $vdr = new VehicleDailyReport();
                            $vdr->reg_no = $vehicle->reg_no;
                            $vdr->vehicle_plate = $vehicle->vehicle_plate;
                            $vdr->date = Carbon::now();
                            $vdr->distance = isset($obj->TongKmNgay) ? $obj->TongKmNgay * 1000 : ''; // Đơn vị met
                            $vdr->save();
                        }
                    }
                }
            }
        } catch (\Exception $exception) {
            $gpsSyncLog->error_code = 'Exception';
            $gpsSyncLog->error_message = $exception->getMessage();
        }
        $gpsSyncLog->type_request = 'doDailyAdsun ALL';
        $gpsSyncLog->save();
    }

    public function doDailyEposi()
    {
        $gpsSyncLog = new GpsSyncLog();
        try {
            $client = new \GuzzleHttp\Client();

            $request = $client->get(env('GPS_EPOSI_URL_ALL', 'http://qc31.vn/rest/api/v2/vehicle/list/state'), ['auth' => [
                env('GPS_EPOSI_USER', 'giatruong'), env('GPS_EPOSI_PASSWORD', 'giatruong2018')
            ]]);
            $response = $request->getBody();
            $gpsSyncLog->request = json_encode(['request' => env('GPS_EPOSI_URL_ALL', 'http://qc31.vn/rest/api/v2/vehicle/list/state')]);

            if ($response != null) {
                $content = $response->getContents();
                $gpsSyncLog->response = 'doDailyEposi OK';
                $data = json_decode($content);
                if (!empty($data)) {
                    foreach ($data->data as $obj) {
                        $vehicle = $this->getVehicleRepos()->getVehicleByGpsCompanyAndVehiclePlate(config('constant.GC_EPOSI'), $obj->id);
                        if (isset($vehicle)) {
                            $vdr = new VehicleDailyReport();
                            $vdr->gps_id = $vehicle->gps_id;
                            $vdr->reg_no = $vehicle->reg_no;
                            $vdr->vehicle_plate = $vehicle->vehicle_plate;
                            $vdr->date = Carbon::now();
                            $vdr->distance = isset($obj->kmInDay) ? $obj->kmInDay * 1000 : ''; // Đơn vị met
                            $vdr->save();
                        }
                    }
                }
            }
        } catch (\Exception $exception) {
            $gpsSyncLog->error_code = 'Exception';
            $gpsSyncLog->error_message = $exception->getMessage();
        }
        $gpsSyncLog->type_request = 'doDailyEposi ALL';
        $gpsSyncLog->save();
    }

    public function doDailyBinhAnh2()
    {
        $gpsSyncLog = new GpsSyncLog();
        try {
            $client = new \GuzzleHttp\Client(['headers' => ['Content-Type' => 'application/json']]);
            $request = $client->post('http://api.gps.binhanh.vn/api/gps/tracking', ['body' => json_encode(
                [
                    'CustomerCode' => env('GPS_BINH_ANH_2_ACCOUNT'),
                    'key' => env('GPS_BINH_ANH_2_KEY')
                ]
            )]);
            $response = $request->getBody();
            $gpsSyncLog->request = json_encode(['request' => 'http://api.gps.binhanh.vn/api/gps/tracking']);

            if ($response != null) {
                $content = $response->getContents();
                $gpsSyncLog->response = 'doDailyBinhAnh2 OK';
                $data = json_decode($content);
                if (!empty($data)) {
                    foreach ($data->Vehicles as $obj) {
                        $vehicle = $this->getVehicleRepos()->getVehicleByGpsCompanyAndVehiclePlate(config('constant.GC_BINH_ANH_2'), $obj->VehiclePlate);
                        if (isset($vehicle)) {
                            $vdr = new VehicleDailyReport();
                            $vdr->gps_id = $vehicle->gps_id;
                            $vdr->reg_no = $vehicle->reg_no;
                            $vdr->vehicle_plate = $vehicle->vehicle_plate;
                            $vdr->date = Carbon::now();
                            $vdr->distance = isset($obj->TotalKm) ? $obj->TotalKm * 1000 : ''; // Đơn vị met
                            $vdr->save();
                        }
                    }
                }
            }
        } catch (\Exception $exception) {
            $gpsSyncLog->error_code = 'Exception';
            $gpsSyncLog->error_message = $exception->getMessage();
        }
        $gpsSyncLog->type_request = 'doDailyBinhAnh2 ALL';
        $gpsSyncLog->save();
    }

    public function doDailyVietMaps()
    {
        // VietMaps GPS
        $now = Carbon::now()->format('Ymd');
        // Lay tat ca cac xe co GPS VietMap
        $query = DB::table('vehicle')->where([
            ['gps_company_id', '=', config('constant.GC_VIET_MAPS')],
            ['del_flag', '=', '0']
        ])->whereNotNull('gps_id');
        $vehicles = $query->get();
        if (null != $vehicles && 0 < sizeof($vehicles)) {
            foreach ($vehicles as $vehicle) {
                $r = new Request();
                $r['from'] = $now;
                $r['to'] = $now;
                $r['gpsId'] = $vehicle->gps_id;
                $r['gpsCompanyId'] = config('constant.GC_VIET_MAPS');

                $out = $this->getDailyReportsByVehicle($r)->getData();
                if (null != $out && $out->errorCode == HttpCode::EC_OK && 0 < sizeof($out->data)) {
                    $obj = $out->data[0];

                    $vdr = new VehicleDailyReport();
                    $vdr->gps_id = $vehicle->gps_id;
                    $vdr->reg_no = $vehicle->reg_no;
                    $vdr->vehicle_plate = $vehicle->vehicle_plate;
                    $vdr->date = Carbon::now();
                    $vdr->distance = isset($obj->Distance) ? $obj->Distance : '';
                    $vdr->door_open_count = isset($obj->DoorOpenCount) ? $obj->DoorOpenCount : '';
                    $vdr->over_speed_count = isset($obj->OverSpeedCount) ? $obj->OverSpeedCount : '';
                    $vdr->max_speed = isset($obj->MaxSpeed) ? $obj->MaxSpeed : '';
                    $vdr->first_acc_on_time = isset($obj->FirstAccOnTime) ? $obj->FirstAccOnTime : '';
                    $vdr->last_acc_off_time = isset($obj->LastAccOffTime) ? $obj->LastAccOffTime : '';
                    $vdr->acc_time = isset($obj->AccTime) ? $obj->AccTime : '';
                    $vdr->run_time = isset($obj->RunTime) ? $obj->RunTime : '';
                    $vdr->idle_time = isset($obj->IdleTime) ? $obj->IdleTime : '';
                    $vdr->stop_time = isset($obj->StopTime) ? $obj->StopTime : '';
                    $vdr->sys_gps_time = isset($obj->SysTime) ? $obj->SysTime : '';
                    $vdr->date_gps_return = isset($obj->Date) ? $obj->Date : '';
                    $vdr->save();
                }
            }
        }
    }

    // Lấy dữ liệu xe
    public function doDailyADA()
    {
        $gpsSyncLog = new GpsSyncLog();
        $client = new \GuzzleHttp\Client(['headers' => ['X-API-KEY' => env('GPS_ADA_API_KEY')]]);
        $request = $client->get('http://apiv4.adagps.com/index.php/GetTrackingData?username=' . env('GPS_ADA_USERNAME'));
        $response = $request->getBody();
        $gpsSyncLog->request = json_encode(['request' => 'http://apiv4.adagps.com/index.php/GetTrackingData?username=' . env('GPS_ADA_USERNAME')]);

        if ($response != null) {
            $content = $response->getContents();
            $gpsSyncLog->response = 'doDailyADA OK';
            $data = json_decode($content);
            if (!empty($data)) {
                foreach ($data->data as $obj) {
                    $vehicle = $this->getVehicleRepos()->getVehicleByGpsCompanyAndGpsId(config('constant.GC_ADA'), $obj->vehicle_id);
                    if (isset($vehicle)) {
                        $vdr = new VehicleDailyReport();
                        $vdr->gps_id = $vehicle->gps_id;
                        $vdr->reg_no = $vehicle->reg_no;
                        $vdr->vehicle_plate = $vehicle->vehicle_plate;
                        $vdr->date = Carbon::now();
                        $vdr->distance = isset($obj->distance_on_day) ? $obj->distance_on_day * 1000 : ''; // Đơn vị met
                        $vdr->door_open_count = isset($obj->door_is_open) ? $obj->door_is_open : ''; // 1 or 0
                        $vdr->over_speed_count = isset($obj->over_speed_count_on_day) ? $obj->over_speed_count_on_day : '';
                        $vdr->max_speed = isset($obj->v_limit_max) ? $obj->v_limit_max : '';
                        $vdr->run_time = isset($obj->one_day_running_time) ? $obj->one_day_running_time : '';
                        $vdr->stop_time = isset($obj->stop_times_on_day) ? $obj->stop_times_on_day : '';
                        $vdr->date_gps_return = isset($obj->trk_time) ? $obj->trk_time : '';

                        $vdr->save();
                    }
                }
            }
            $gpsSyncLog->save();
        }
    }

    public function doDailyEupfin()
    {
        // Eupfin GPS
        $query = DB::table('vehicle')->where([
            ['gps_company_id', '=', config('constant.GC_EUPFIN')],
            ['del_flag', '=', '0']
        ]);
        $vehicles = $query->get();
        if (null != $vehicles && 0 < sizeof($vehicles)) {
            foreach ($vehicles as $vehicle) {
                $r = new Request();
                $r['from'] = Carbon::today()->startOfDay()->format('Y-m-d H:i:s');
                $r['to'] = Carbon::today()->endOfDay()->format('Y-m-d H:i:s');;
                $r['vehicle_plate'] = $vehicle->vehicle_plate;
                $r['gpsCompanyId'] = config('constant.GC_EUPFIN');

                $out = $this->getDailyReportsByVehicle($r)->getData();
                if (null != $out && $out->errorCode == HttpCode::EC_OK) {
                    $obj = $out->data->result;

                    $vdr = new VehicleDailyReport();
                    $vdr->gps_id = $vehicle->gps_id;
                    $vdr->reg_no = $vehicle->reg_no;
                    $vdr->vehicle_plate = $vehicle->vehicle_plate;
                    $vdr->date = Carbon::now();
                    $vdr->distance = isset($obj->TotalMile) ? $obj->TotalMile * 1609 : ''; // Đơn vị met
                    $vdr->save();
                }
            }
        }
    }

    public function doDailyVComSat()
    {
        $query = DB::table('vehicle')->where([
            ['gps_company_id', '=', config('constant.GC_VCOMSAT')],
            ['del_flag', '=', '0']
        ]);
        $vehicles = $query->get();
        if (null != $vehicles && 0 < sizeof($vehicles)) {
            foreach ($vehicles as $vehicle) {
                $r = new Request();
                $r['from'] = Carbon::today()->startOfDay()->format('Y-m-d\TH:i:s');
                $r['to'] = Carbon::today()->endOfDay()->format('Y-m-d\TH:i:s');
                $r['vehicle_plate'] = $vehicle->reg_no;
                $r['gpsCompanyId'] = config('constant.GC_VCOMSAT');

                $out = $this->getDailyReportsByVehicle($r)->getData();
                if (null != $out && $out->errorCode == HttpCode::EC_OK) {
                    $obj = $out->data;

                    $vdr = new VehicleDailyReport();
                    $vdr->gps_id = $vehicle->gps_id;
                    $vdr->reg_no = $vehicle->reg_no;
                    $vdr->vehicle_plate = $vehicle->vehicle_plate;
                    $vdr->date = Carbon::now();
                    $vdr->distance = isset($obj->Km) ? $obj->Km * 1000 : ''; // Đơn vị Met
                    $vdr->save();
                }
            }
        }
    }

    public function scheduleDistanceReportDaily($processDate)
    {
        try {
            $enableGps = env('GPS_STATUS', false);
            if ($enableGps) {
                $this->getDistanceDailyReportRepository()->deleteWhere([
                    'date' => $processDate
                ]);

                $GCs = explode(',', env('GPS_COMPANY'));
                foreach ($GCs as $gc) {
                    $upTimeVehicle = $this->getDistanceDailyReportRepository()->getUpTimeVehicleDaily($processDate, $gc);
                    if ($gc == config('constant.GC_VIET_MAPS')) {
                        if (isset($upTimeVehicle) && sizeof($upTimeVehicle) > 0) {
                            foreach ($upTimeVehicle as $item) {
                                $r = new Request();
                                $from = Carbon::parse($item->ETD_reality);
                                $r['from'] = $from->format('YmdHis');
                                $to = Carbon::parse($item->ETA_reality);
                                $r['to'] = $to->format('YmdHis');
                                $r['gpsId'] = $item->gps_id;
                                $r['vehicle_plate'] = $item->vehicle_plate;
                                $r['gpsCompanyId'] = $gc;

                                $out = $this->getDistanceReport($r)->getData();

                                if (null != $out && $out->errorCode == HttpCode::EC_OK && 0 < sizeof($out->data)) {
                                    $obj = $out->data[sizeof($out->data) - 1];

                                    $entity = $this->createDistanceReport($item, $processDate, $obj->Distance, $gc);

                                    //Lấy lại dữ liệu tổng km đi dc
                                    $fromDate = Carbon::parse($processDate . ' 00:00');
                                    $toDate = Carbon::parse($processDate . ' 23:59');
                                    if ($fromDate == $from && $toDate == $to) {
                                        $entity->distance = $obj->Distance;
                                    } else {
                                        $r['from'] = $fromDate->format('YmdHis');
                                        $r['to'] = $toDate->format('YmdHis');
                                        $out = $this->getDistanceReport($r)->getData();

                                        if (null != $out && $out->errorCode == HttpCode::EC_OK && 0 < sizeof($out->data)) {
                                            $obj = $out->data[sizeof($out->data) - 1];
                                            $entity->distance = $obj->Distance;
                                        }
                                    }
                                    unset($out);
                                    $entity->save();
                                }
                            }
                        }
                    } else if ($gc == config('constant.GC_EUPFIN')) {
                        if (isset($upTimeVehicle) && sizeof($upTimeVehicle) > 0) {
                            foreach ($upTimeVehicle as $item) {
                                $r = new Request();
                                $from = Carbon::parse($item->ETD_reality);
                                $r['from'] = $from->format('Y-m-d H:i:s');
                                $to = Carbon::parse($item->ETA_reality);
                                $r['to'] = $to->format('Y-m-d H:i:s');
                                $r['gpsId'] = $item->gps_id;
                                $r['vehicle_plate'] = $item->vehicle_plate;
                                $r['gpsCompanyId'] = $gc;

                                $out = $this->getDistanceReport($r)->getData();

                                if (null != $out && $out->errorCode == HttpCode::EC_OK && 0 < sizeof($out->data)) {
                                    $obj = $out->data->result;

                                    $entity = $this->createDistanceReport($item, $processDate, isset($obj->TotalMile) ? $obj->TotalMile * 1609 : 0, $gc);
                                    $entity->save();
                                }
                            }
                        }
                    } else if ($gc == config('constant.GC_VCOMSAT')) {
                        if (isset($upTimeVehicle) && sizeof($upTimeVehicle) > 0) {
                            foreach ($upTimeVehicle as $item) {
                                $r = new Request();
                                $from = Carbon::parse($item->ETD_reality);
                                $r['from'] = $from->format('Y-m-d\TH:i:s');
                                $to = Carbon::parse($item->ETA_reality);
                                $r['to'] = $to->format('Y-m-d\TH:i:s');
                                $r['gpsId'] = $item->gps_id;
                                $r['vehicle_plate'] = $item->reg_no;
                                $r['gpsCompanyId'] = $gc;

                                $out = $this->getDistanceReport($r)->getData();
                                if (null != $out && $out->errorCode == HttpCode::EC_OK) {
                                    $obj = $out->data;

                                    $entity = $this->createDistanceReport($item, $processDate, isset($obj->Km) ? $obj->Km * 1000 : 0, $gc);
                                    $entity->save();
                                }
                            }
                        }
                    } else if ($gc == config('constant.GC_ADA')) {
                        if (isset($upTimeVehicle) && sizeof($upTimeVehicle) > 0) {
                            foreach ($upTimeVehicle as $item) {
                                $r = new Request();
                                $from = Carbon::parse($item->ETD_reality);
                                $r['from'] = $from->format('Y-m-d H:i:s');
                                $to = Carbon::parse($item->ETA_reality);
                                $r['to'] = $to->format('Y-m-d H:i:s');
                                $r['gpsId'] = $item->gps_id;
                                $r['vehicle_plate'] = $item->reg_no;
                                $r['gpsCompanyId'] = $gc;

                                $out = $this->getDistanceReport($r)->getData();
                                if (null != $out && $out->errorCode == HttpCode::EC_OK) {
                                    $obj = $out->data;

                                    $entity = $this->createDistanceReport($item, $processDate, isset($obj->distance_on_day) ? $obj->distance_on_day * 1000 : 0, $gc);
                                    $entity->save();
                                }
                            }
                        }
                    } else if ($gc == config('constant.GC_BINH_ANH_2')) {
                        if (isset($upTimeVehicle) && sizeof($upTimeVehicle) > 0) {
                            foreach ($upTimeVehicle as $item) {
                                $r = new Request();
                                $from = Carbon::parse($item->ETD_reality);
                                $r['from'] = $from->format('Y-m-d H:i:s');
                                $to = Carbon::parse($item->ETA_reality);
                                $r['to'] = $to->format('Y-m-d H:i:s');
                                $r['gpsId'] = $item->gps_id;
                                $r['vehicle_plate'] = $item->vehicle_plate;
                                $r['gpsCompanyId'] = $gc;

                                $out = $this->getDistanceReport($r)->getData();
                                if (null != $out && $out->errorCode == HttpCode::EC_OK) {
                                    $obj = $out->data;

                                    $entity = $this->createDistanceReport($item, $processDate, isset($obj->Kmgps) ? $obj->Kmgps * 1000 : 0, $gc);
                                    $entity->save();
                                }
                            }
                        }
                    } else if ($gc == config('constant.GC_EPOSI')) {

                        if (isset($upTimeVehicle) && sizeof($upTimeVehicle) > 0) {
                            foreach ($upTimeVehicle as $item) {
                                $r = new Request();
                                $from = Carbon::parse($item->ETD_reality);
                                $r['from'] = sprintf('%d', $from->getPreciseTimestamp(3));
                                $to = Carbon::parse($item->ETA_reality);
                                $r['to'] = sprintf('%d', $to->getPreciseTimestamp(3));
                                $r['gpsId'] = $item->gps_id;
                                $r['vehicle_plate'] = $item->vehicle_plate;
                                $r['gpsCompanyId'] = $gc;

                                $out = $this->getDistanceReport($r)->getData();
                                if (null != $out && $out->errorCode == HttpCode::EC_OK) {
                                    $obj = $out->data;

                                    $entity = $this->createDistanceReport($item, $processDate, isset($obj->totalKm) ? round($obj->totalKm * 1000) : 0, $gc);
                                    $entity->save();
                                }
                            }
                        }
                    } else if ($gc == config('constant.GC_VINHHIEN')) {
                        if (isset($upTimeVehicle) && sizeof($upTimeVehicle) > 0) {
                            foreach ($upTimeVehicle as $item) {
                                $from = $this->getVinhhienGpsRepos()->getCurrentTotalKmByTime($item->vehicle_plate, $item->ETD_reality);
                                $to = $this->getVinhhienGpsRepos()->getCurrentTotalKmByTime($item->vehicle_plate, $item->ETA_reality);
                                if ($from && $to) {
                                    $entity = $this->createDistanceReport($item, $processDate, $to->current_total_km - $from->current_total_km, $gc);
                                    $entity->save();
                                }
                            }
                        }
                    } else {
                        //Công ty GPS không cung cấp số km có hàng
                        foreach ($upTimeVehicle as $item) {
                            $entity = $this->createDistanceReport($item, $processDate, 0, $gc);
                            $entity->status = '0'; // Chưa hoàn thành tính số KM
                            $entity->save();
                        }
                    }

                    //Lưu distance_with_goods vào bảng chuyến
                    if ($upTimeVehicle) {
                        $routeIds = array_column($upTimeVehicle, 'route_id');
                        $distanceByRoute = $this->getDistanceDailyReportRepository()->getDistanceByRoutes($routeIds);
                        if ($distanceByRoute) {
                            foreach ($distanceByRoute as $item) {
                                $routeEntity = $this->getRouteRepos()->getItemById($item->route_id);
                                if ($routeEntity) {
                                    $routeEntity->gps_distance = $item->distance_with_goods;
                                    $routeEntity->save();
                                }
                            }
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            logError($e);
        }
    }

    public function createDistanceReport($item, $processDate, $distanceWithGoods, $gpsCompanyId)
    {
        $entity = new DistanceDailyReport();
        $entity->route_id = $item->route_id;
        $entity->vehicle_id = $item->vehicle_id;
        $entity->gps_id = $item->gps_id;
        $entity->reg_no = $item->reg_no;
        $entity->vehicle_plate = $item->vehicle_plate;
        $entity->date = $processDate;
        $entity->distance = $item->distance;
        $entity->distance_with_goods = $distanceWithGoods;
        $entity->from_time = $item->ETD_reality;
        $entity->to_time = $item->ETA_reality;
        $entity->gps_company_id = $gpsCompanyId;

        return $entity;
    }

    public function getDistanceReport(Request $request)
    {
        try {
            $result = [];
            $gc = $request['gpsCompanyId'];
            $gpsSyncLog = new GpsSyncLog();
            if ($gc == config('constant.GC_VIET_MAPS')) {
                $gpsId = $request['gpsId'];
                $from = $request['from'];
                $to = $request['to'];
                $gpsSyncLog = new GpsSyncLog();

                try {
                    $client = new \GuzzleHttp\Client();
                    $request = $client->get('https://client-api.quanlyxe.vn/v3/tracking/getvehiclehistory?id=' . $gpsId . '&from=' . $from . '&to=' . $to . '&apikey=' . env('GPS_VIETMAPS_API_KEY'));
                    $response = $request->getBody();
                    $gpsSyncLog->request = json_encode(['request' => 'https://client-api.quanlyxe.vn/v3/tracking/getvehiclehistory?id=' . $gpsId . '&from=' . $from . '&to=' . $to . '&apikey=' . env('GPS_VIETMAPS_API_KEY')]);

                    if ($response != null) {
                        $content = $response->getContents();
                        $data = json_decode($content);
                        if (!empty($data)) {
                            $result = $data->Data;
                        }
                    }
                } catch (\Exception $exception) {
                    $gpsSyncLog->error_code = 'Exception';
                    $gpsSyncLog->error_message = $exception->getMessage();
                }
                $gpsSyncLog->type_request = 'getDistanceReport';
                $gpsSyncLog->save();
            } else if ($gc == config('constant.GC_EUPFIN')) {
                $startTime = $request['from'];
                $endTime = $request['to'];
                $vehicleNo = $request['vehicle_plate'];

                try {
                    $client = new \GuzzleHttp\Client(['headers' => ['X-Eupfin-Api-Key' => env('GPS_EUPFIN_API_KEY')]]);
                    $body['account'] = env('GPS_EUPFIN_ACCOUNT');
                    $body['password'] = env('GPS_EUPFIN_PASSWORD');
                    $body['vehicleNo'] = $vehicleNo;
                    $body['startTime'] = $startTime;
                    $body['endTime'] = $endTime;

                    $request = $client->post(env('GPS_EUPFIN_URL_HISTORY', 'http://api.eup.net.vn:8000/thanhdat/history'), ['form_params' => $body]);
                    $response = $request->getBody();
                    $gpsSyncLog->request = json_encode(['request' => env('GPS_EUPFIN_URL_HISTORY', 'http://api.eup.net.vn:8000/thanhdat/history')]);

                    if ($response != null) {
                        $content = $response->getContents();
                        $data = json_decode($content);
                        if (!empty($data)) {
                            $result = $data;
                        }
                    }
                } catch (\Exception $exception) {
                    $gpsSyncLog->error_code = 'Exception';
                    $gpsSyncLog->error_message = $exception->getMessage();
                }
                $gpsSyncLog->type_request = 'GetDailyReports';
                $gpsSyncLog->save();
            } else if ($gc == config('constant.GC_VCOMSAT')) {
                try {
                    $startTime = $request['from'];
                    $endTime = $request['to'];
                    $vehicleNo = $request['vehicle_plate'];

                    $client = new SoapClient(env('GPS_VCOMSAT_WEB_SERVICE_WSDL', 'http://stagingws.giamsathanhtrinh.vn/SmartLog.asmx?WSDL'));
                    $auth = array(
                        'Username' => env('GPS_VCOMSAT_USER_HEADER', 'smartlog'),
                        'Password' => env('GPS_VCOMSAT_PASSWORD_HEADER', 'p@ssw0rd'),
                    );
                    $header = new SoapHeader('http://tempuri.org/', 'ServiceAuthHeader', $auth, false);
                    $client->__setSoapHeaders($header);

                    $params = array(
                        'CarPlate' => $vehicleNo,
                        'FromDate' => $startTime,
                        'ToDate' => $endTime,
                    );
                    $gpsSyncLog->request = json_encode(['request' => $params]);
                    $response = $client->__soapCall(env('GPS_VCOMSAT_FUNCTION_HISTORY_NAME', 'GetCarSignalByPlate'), array($params));

                    if (isset($response)) {
                        $getCarInfoResult = $response->GetCarSignalByPlateResult;
                        if (isset($getCarInfoResult)) {
                            $carReviewSignal = $getCarInfoResult->CarReviewSignal;
                            if (isset($carReviewSignal) & sizeof($carReviewSignal) > 0) {
                                $gpsSyncLog->error_message = 'OK';
                                $gpsSyncLog->response = '';
                                $result = $carReviewSignal[sizeof($carReviewSignal) - 1]; // lấy bản ghi cuối cùng
                            } else {
                                $gpsSyncLog->error_message = '$carReviewSignal empty';
                            }
                        } else {
                            $gpsSyncLog->error_message = '$getCarInfoResult empty';
                        }
                    } else {
                        $gpsSyncLog->error_message = '$response empty';
                    }
                } catch (\Exception $exception) {
                    $gpsSyncLog->error_code = 'Exception';
                    $gpsSyncLog->error_message = $exception->getMessage();
                }
                $gpsSyncLog->type_request = 'ALL';
                $gpsSyncLog->save();
            } else if ($gc == config('constant.GC_ADA')) {
                $gpsId = $request['gpsId'];
                $from = $request['from'];
                $to = $request['to'];
                $gpsSyncLog = new GpsSyncLog();

                try {
                    $client = new \GuzzleHttp\Client(['headers' => ['X-API-KEY' => env('GPS_ADA_API_KEY')]]);
                    $request = $client->get('http://apiv4.adagps.com/index.php/GetHistoryData?username=' . env('GPS_ADA_USERNAME') . '&vehicleid=' . $gpsId . '&fromdate=' . $from . '&todate=' . $to);

                    $response = $request->getBody();
                    $gpsSyncLog->request = json_encode(['request' => 'http://apiv4.adagps.com/index.php/GetHistoryData?username=' . env('GPS_ADA_USERNAME') . '&vehicleid=' . $gpsId . '&fromdate=' . $from . '&todate=' . $to]);

                    if ($response != null) {
                        $content = $response->getContents();
                        $gpsSyncLog->response = 'OK';
                        $data = json_decode($content);
                        if (!empty($data) && 0 < sizeof($data->data)) {
                            $result = $data->data[sizeof($data->data) - 1]; // Lấy bản ghi cuối cùng
                        }
                    }
                } catch (\Exception $exception) {
                    $gpsSyncLog->error_code = 'Exception';
                    $gpsSyncLog->error_message = $exception->getMessage();
                }
                $gpsSyncLog->type_request = 'getDistanceReport';
                $gpsSyncLog->save();
            } else if ($gc == config('constant.GC_BINH_ANH_2')) {
                $gpsId = $request['gpsId'];
                $from = $request['from'];
                $to = $request['to'];
                $vehicleNo = $request['vehicle_plate'];
                $gpsSyncLog = new GpsSyncLog();

                try {
                    $client = new \GuzzleHttp\Client(['headers' => ['Content-Type' => 'application/json']]);
                    $request = $client->post('http://api.gps.binhanh.vn//api/gps/route', ['body' => json_encode(
                        [
                            'CustomerCode' => env('GPS_BINH_ANH_2_ACCOUNT'),
                            'key' => env('GPS_BINH_ANH_2_KEY'),
                            'vehiclePlate' => $vehicleNo,
                            'fromDate' => $from,
                            'toDate' => $to,
                        ]
                    )]);
                    $response = $request->getBody();
                    $gpsSyncLog->request = json_encode(['request' => 'http://api.gps.binhanh.vn//api/gps/route']);

                    if ($response != null) {
                        $content = $response->getContents();
                        $gpsSyncLog->response = $content;
                        $data = json_decode($content);
                        if (!empty($data) && 0 < sizeof($data->Routes)) {
                            $result = $data->Routes[sizeof($data->Routes) - 1]; // Lấy bản ghi cuối cùng
                        }
                    }
                } catch (\Exception $exception) {
                    $gpsSyncLog->error_code = 'Exception';
                    $gpsSyncLog->error_message = $exception->getMessage();
                }
                $gpsSyncLog->type_request = 'getDistanceReport';
                $gpsSyncLog->save();
            } else if ($gc == config('constant.GC_EPOSI')) {
                $gpsId = $request['gpsId'];
                $from = $request['from'];
                $to = $request['to'];
                $vehicleNo = $request['vehicle_plate'];
                $gpsSyncLog = new GpsSyncLog();

                try {
                    $client = new \GuzzleHttp\Client();

                    $url = "http://qc31.vn/rest/api/v2/report/" . $vehicleNo . "/trip/range?from=" . $from . "&to=" . $to;
                    $request = $client->get($url, ['auth' => [
                        env('GPS_EPOSI_USER', 'giatruong'), env('GPS_EPOSI_PASSWORD', 'giatruong2018')
                    ]]);
                    $response = $request->getBody();
                    $gpsSyncLog->request = json_encode(['request' => $url]);

                    if ($response != null) {
                        $content = $response->getContents();
                        $gpsSyncLog->response = 'OK';
                        $data = json_decode($content);
                        if (!empty($data) && 0 < sizeof($data->data)) {
                            // Eposi chia lịch sử thành các quãng đường ==>> Lấy tổng KM của các quãng đường
                            $result = $data->data[sizeof($data->data) - 1]; // Lấy bản ghi cuối cùng
                            $total = 0;
                            foreach ($data->data as $it) {
                                $total += $it->tripKm;
                            }
                            $result->totalKm = $total;
                        }
                    }
                } catch (\Exception $exception) {
                    $gpsSyncLog->error_code = 'Exception';
                    $gpsSyncLog->error_message = $exception->getMessage();
                }
                $gpsSyncLog->type_request = 'ALL';
                $gpsSyncLog->save();
            }

            return response()->json([
                'errorCode' => HttpCode::EC_OK,
                'errorMessage' => '',
                'data' => $result
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                'errorCode' => HttpCode::EC_APPLICATION_ERROR,
                'errorMessage' => HttpCode::getMessageForCode(HttpCode::EC_APPLICATION_ERROR)
            ]);
        }
    }

    //Tạo file pdf lệnh vận chuyển
    // CreatedBy nlhoang 27/08/2020
    function shippingOrder($id)
    {
        $entity = $this->getRepository()->getItemInfoById($id);
        $data['entity'] = $entity;

        $infos = $this->getSystemConfigRepository()->where('key', 'like', 'company.%')->get();
        $companyName = $infos
            ->filter(function ($value, $key) {
                return $value->key == "company.name";
            });
        $data['companyName'] = $companyName->isEmpty() ? 'Công ty ABC' : $companyName->first()->value;
        $companyAddress = $infos
            ->filter(function ($value, $key) {
                return $value->key == "company.address";
            });
        $data['companyAddress'] = $companyAddress->isEmpty() ? 'Việt Nam' : $companyAddress->first()->value;

        $companyMobileNo = $infos
            ->filter(function ($value, $key) {
                return $value->key == "company.mobile_no";
            });
        $data['companyMobileNo'] = $companyMobileNo->isEmpty() ? '0999.999.999' : $companyMobileNo->first()->value;

        $companyStamp = $infos
            ->filter(function ($value, $key) {
                return $value->key == "company.stamp";
            })->first();

        $stamp_path = '';
        if (!empty($companyStamp)) {
            $file = File::where('file_id', $companyStamp->value)->first();
            $stamp_path = empty($file) ? '' : public_path($file->path);
        }
        $data['companyStampPath'] = $stamp_path;

        $pdf = PDF::loadView('backend.route.shipping_order', $data)->setPaper('a4')->setWarnings(false);

        return $pdf->stream('LENH_VAN_CHUYEN.pdf')->header('Content-Type', 'application/pdf');
    }

    // Lấy bảng tổng quan chi phí
    // CreatedBy nlhoang 25.09.2020
    public function getCostOverviewReport(Request $request)
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
                $fromDate = $request->get('fromDate');
                $toDate = $request->get('toDate');
                $userId = Auth::user()->id;
                $driver = $this->getDriverRepos()->getFullInfoDriverWithUserId($userId);
                $results = $this->getRepository()->getCostOverviewReport($driver->id, $fromDate, $toDate);
                return response()->json([
                    'errorCode' => HttpCode::EC_OK,
                    'errorMessage' => '',
                    'data' => $results
                ]);
            }
        } catch (\Exception $exception) {
            logError($exception);
            return response()->json([
                'errorCode' => HttpCode::EC_APPLICATION_ERROR,
                'errorMessage' => HttpCode::getMessageForCode(HttpCode::EC_APPLICATION_ERROR)
            ]);
        }
    }
}
