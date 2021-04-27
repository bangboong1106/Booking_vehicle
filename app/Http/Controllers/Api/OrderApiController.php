<?php

namespace App\Http\Controllers\Api;

use App\Common\AppConstant;
use App\Common\HttpCode;
use App\Http\Controllers\Base\ApiController;
use App\Model\Entities\Order;
use App\Model\Entities\OrderFile;
use App\Model\Entities\OrderGoods;

use App\Repositories\AdminUserInfoRepository;
use App\Repositories\CustomerRepository;
use App\Repositories\DriverRepository;
use App\Repositories\FileRepository;
use App\Repositories\LocationRepository;
use App\Repositories\OrderCustomerRepository;
use App\Repositories\OrderFileRepository;
use App\Repositories\OrderHistoryRepository;
use App\Repositories\OrderRepository;
use App\Repositories\RouteCostRepository;
use App\Repositories\RoutesRepository;
use App\Repositories\TPActionSyncRepository;
use App\Repositories\VehicleRepository;
use App\Repositories\SystemConfigRepository;

use App\Services\NotificationService;
use DateTime;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Input;
use JWTAuth;
use Mockery\Exception;
use Validator;

class OrderApiController extends ApiController
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
    protected $orderCustomerRepos;
    protected $locationRepos;
    protected $tpActionSyncRepos;
    protected $systemConfigRepository;
    protected $adminUserInfoRepos;
    protected $notificationService;

    /**
     * @return OrderRepository
     */
    public function getOrderRepos()
    {
        return $this->orderRepos;
    }

    public function setOrderRepos($orderRepos)
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

    public function getOrderCustomerRepos()
    {
        return $this->orderCustomerRepos;
    }

    public function setOrderCustomerRepos($orderCustomerRepos)
    {
        $this->orderCustomerRepos = $orderCustomerRepos;
    }

    public function getLocationRepos()
    {
        return $this->locationRepos;
    }

    public function setLocationRepos($locationRepos)
    {
        $this->locationRepos = $locationRepos;
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
     * @return TPActionSyncRepository
     */
    public function getSystemConfigRepository()
    {
        return $this->systemConfigRepository;
    }

    /**
     * @param $systemConfigRepository
     */
    public function setSystemConfigRepository($systemConfigRepository)
    {
        $this->systemConfigRepository = $systemConfigRepository;
    }

    /**
     * @return mixed
     */
    public function getAdminUserInfoRepos()
    {
        return $this->adminUserInfoRepos;
    }

    /**
     * @param mixed $adminUserInfoRepos
     */
    public function setAdminUserInfoRepos($adminUserInfoRepos): void
    {
        $this->adminUserInfoRepos = $adminUserInfoRepos;
    }

    /**
     * @return mixed
     */
    public function getNotificationService()
    {
        return $this->notificationService;
    }

    /**
     * @param mixed $notificationService
     */
    public function setNotificationService($notificationService): void
    {
        $this->notificationService = $notificationService;
    }

    public function __construct(
        OrderRepository $orderRepository,
        FileRepository $fileRepository,
        OrderFileRepository $orderFileRepository,
        DriverRepository $driverRepository,
        OrderHistoryRepository $orderHistoryRepository,
        CustomerRepository $customerRepository,
        VehicleRepository $vehicleRepos,
        RoutesRepository $routesRepository,
        RouteCostRepository $routeCostRepos,
        OrderCustomerRepository $orderCustomerRepository,
        LocationRepository $locationRepository,
        TPActionSyncRepository $tpActionSyncRepository,
        SystemConfigRepository $systemConfigRepository,
        AdminUserInfoRepository $adminUserInfoRepository,
        NotificationService $notificationService
    )
    {
        parent::__construct();
        $this->setOrderRepos($orderRepository);
        $this->setFileRepos($fileRepository);
        $this->setOrderFileRepos($orderFileRepository);
        $this->setDriverRepos($driverRepository);
        $this->setOrderHistoryRepos($orderHistoryRepository);
        $this->setCustomerRepos($customerRepository);
        $this->setVehicleRepos($vehicleRepos);
        $this->setRouteRepos($routesRepository);
        $this->setRouteCostRepos($routeCostRepos);
        $this->setOrderCustomerRepos($orderCustomerRepository);
        $this->setLocationRepos($locationRepository);
        $this->setTPActionSyncRepos($tpActionSyncRepository);
        $this->setSystemConfigRepository($systemConfigRepository);
        $this->setAdminUserInfoRepos($adminUserInfoRepository);
        $this->setNotificationService($notificationService);
    }

    public function getOrders(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'pageSize' => 'required',
                'pageIndex' => 'required',
                'status' => 'required',
                'textSearch' => '',
                'fromDate' => '',
                'toDate' => '',
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
                $userId = Auth::User()->id;
                $request['pageSize'] = 100;
                $driverObj = $this->getDriverRepos()->getFullInfoDriverWithUserId($userId);
                $orders = $this->getDriverRepos()->getOrdersByDriverIdAndStatus($driverObj->id, $request);

                return response()->json([
                    'errorCode' => HttpCode::EC_OK,
                    'errorMessage' => '',
                    'data' => $orders
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

    public function getOrderDetail(Request $request)
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
                $userId = Auth::User()->id;
                $driver = $this->getDriverRepos()->getDriverByUserId($userId);
                $order = $this->getOrderRepos()->getOrderByID($request['id'], $driver->id);

                return response()->json([
                    'errorCode' => HttpCode::EC_OK,
                    'errorMessage' => '',
                    'data' => $order
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

    public function updateOrders(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'orderId' => '',
                'status' => '',
                'remark' => '',
                'files' => '',
                'extendCost' => ''
            ]);
            if ($validation->fails()) {
                return response()->json([
                    'errorCode' => HttpCode::EC_BAD_REQUEST,
                    'errorMessage' => $validation->messages()
                ]);
            } else {

                DB::beginTransaction();

                $order = $this->getOrderRepos()->getModelById($request['orderId']);

                if (null == $order || empty($order)) {
                    return response()->json([
                        'errorCode' => HttpCode::EC_BAD_REQUEST,
                        'errorMessage' => HttpCode::getMessageForCode(4)
                    ]);
                }
                $orderOld = $order->replicate();

                $currentDay = new DateTime();
                if (isset($request['status']) && !empty($request['status'])) {
                    $order->status = $request['status'];
                    if ($order->status == config('constant.HOAN_THANH')) {
                        $time = 0;
                        $config = $this->getSystemConfigRepository()->search(['key_eq' => 'DriverMobile.CompletedLimitTime'])->get();

                        if (!$config->isEmpty()) {
                            $time = $config->first()->value == null ? 0 : $config->first()->value;
                        }
                        if ($time != 0) {
                            $currentFormat = 'Y-m-d H:i:s';
                            $now = Carbon::now();
                            $reality = Carbon::createFromFormat($currentFormat, $order->ETD_date_reality . ' ' . $order->ETD_time_reality)->addMinutes($time);

                            if ($now->lt($reality)) {
                                return response()->json([
                                    'errorCode' => HttpCode::EC_BAD_REQUEST,
                                    'errorMessage' => 'Đơn hàng của bạn không được xác nhận trong khoảng thời gian ' . $time . ' từ lúc nhận hàng'
                                ]);
                            }
                        }

                        $config = $this->getSystemConfigRepository()->search(['key_eq' => 'DriverMobile.ForceUploadBeforeArrival'])->get();

                        if (!$config->isEmpty() && $config->first()->value == 1) {

                            $orderFiles = OrderFile::where('order_id', '=', $order->id)
                                ->where('file_id', '!=', '')
                                ->first();
                            if ($orderFiles == null) {
                                return response()->json([
                                    'errorCode' => HttpCode::EC_BAD_REQUEST,
                                    'errorMessage' => 'Đơn hàng của bạn chưa có file đính kèm. Vui lòng kiểm tra lại'
                                ]);
                            }
                        }
                    }

                    switch ($request['status']) {
                        case config('constant.DANG_VAN_CHUYEN'):
                            $dateReality = $currentDay->format('Y-m-d');
                            $timeReality = $currentDay->format('H:i');
                            $order->ETD_date_reality = $dateReality;
                            $order->ETD_time_reality = $timeReality;
                            //Cập nhật thời gian nhân hàng cho location đầu tiên ở bảng order location
                            $this->getOrderRepos()->updateDateRealityOrderLocation(
                                $order->id,
                                $order->location_destination_id,
                                $dateReality,
                                $timeReality
                            );
                            break;

                        case config('constant.HOAN_THANH'):
                            $dateReality = $currentDay->format('Y-m-d');
                            $timeReality = $currentDay->format('H:i');
                            $order->ETA_date_reality = $dateReality;
                            $order->ETA_time_reality = $timeReality;
                            //Cập nhật thời gian trả hàng cho location đầu tiên ở bảng order location
                            $this->getOrderRepos()->updateDateRealityOrderLocation(
                                $order->id,
                                $order->location_arrival_id,
                                $dateReality,
                                $timeReality
                            );

                            // Khi hoàn thành: Kiểm tra trạng thái chứng từ. 'DA_THU_DU' => 2,
                            if (isset($request['status_collected_documents']) && config("constant.DA_THU_DU") == $request['status_collected_documents']) {
                                $order->status_collected_documents = $request['status_collected_documents'];
                            }
                            //Cập nhật hạn thu chứng từ
                            $this->getOrderRepos()->updateDateTimeCollectedDocument($order->id, $dateReality, $timeReality);
                            break;

                        case config('constant.HUY'):
                            $order->ETA_date_reality = $currentDay->format('Y-m-d');
                            $order->ETA_time_reality = $currentDay->format('H:i');
                            break;

                        case config('constant.SAN_SANG'):
                            // Notify to admin
                            $order->vehicle_id = null;
                            $order->primary_driver_id = null;
                            $order->secondary_driver_id = null;
                            break;
                    }
                }

                // Notify to admin , partner
                $this->getNotificationService()->notifyDriverToC20AndPartner(1, ['order_status' => $request['status'],
                    'order_id' => $order->id, 'order_code' => $order->order_code, 'customer_id' => $order->customer_id, 'partner_id' => $order->partner_id]);

                $order->remark = $request['remark'];
                $files = $request['files'];
                if (null != $files && !empty($files)) {
                    foreach ($files as $file) {
                        $orderFile = new OrderFile();
                        $orderFile->order_id = $request['orderId'];
                        $orderFile->order_status = isset($request['status']) && !empty($request['status']) ? $request['status'] : $order->status;
                        $orderFile->reason = $request['remark'];
                        if (null != $file && !empty($file)) {
                            $orderFile->file_id = $file['fileId'];
                        }
                        $orderFile->save();
                    }
                } else {
                    $orderFile = new OrderFile();
                    $orderFile->order_id = $request['orderId'];
                    $orderFile->order_status = isset($request['status']) && !empty($request['status']) ? $request['status'] : $order->status;
                    $orderFile->reason = $request['remark'];
                    $orderFile->save();
                }

                $order->save();

                //Trigger tạo bản ghi đồng bộ đối tác
                $this->getTPActionSyncRepos()->triggerActionSync($orderOld, $order);

                //Xư lý chuyến
                if (isset($request['status']) && !empty($request['status']) && ($request['status'] == config('constant.HOAN_THANH') || $request['status'] == config('constant.HUY'))) {
                    $this->_processRouteFromOrderDriver(1, $order->id);
                }

                // Xóa tài xế và xe khỏi trip nếu tài xế không đồng ý xác nhận đơn hàng
                if ($request['status'] == config('constant.SAN_SANG')) {
                    $this->_processRouteFromOrderDriver(2, $order->id);
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
                'errorMessage' => $exception->getMessage()
            ]);
        }
    }

    //Cập nhật chuyến từ tài xế

    /**
     * @param $action 1-Tài xế cập nhật đơn hàng , 2- Tài xế từ chối
     * @param $orderId
     */
    public function _processRouteFromOrderDriver($action, $orderId)
    {
        $order = $this->getOrderRepos()->getItemById($orderId);
        if (!$order)
            return;

        $routeId = $order->route_id;

        //Tài xế từ chối : Xóa đơn khỏi chuyến
        if ($action == 2) {
            $order->route_id = null;
            $order->save();
        }

        $route = $this->getRouteRepos()->getItemById($routeId);
        if ($route) {
            $routeOrders = $this->getOrderRepos()->getOrdersByRouteId($route->id);
            //Xóa chuyến nếu chuyến không có đơn hàng
            if ($routeOrders == null || count($routeOrders) == 0) {
                $this->getRouteCostRepos()->deleteWhere([
                    'route_id' => $route->id
                ]);
                $route->delete();
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
                $orders = $this->getOrderRepos()->getOrdersByRouteId($route->id);

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

    public function getOrderFiles(Request $request)
    {
        try {
            $orderId = $request['id'];

            $attachments = null;
            $order_status_file_list = config("system.order_status_file");;
            foreach ($order_status_file_list as $order_status_file) {
                $attachment = null;
                $attachment['status'] = $order_status_file['id'];
                $order_files = $this->getOrderFileRepos()->getOrderFile($orderId, $order_status_file['id']);
                if ($order_files != null) {
                    $files = null;
                    foreach ($order_files as $key => $order_file) {
                        if (isset($order_file->reason) && !empty($order_file->reason)) {
                            $attachment['note'] = $order_file->reason;
                        }
                        $file = null;
                        if ($order_file->file_id != null) {
                            $fileInfo = app('App\Http\Controllers\Api\FileApiController')->getFile($order_file->file_id);
                            if ($fileInfo != null) {
                                $file['urlThumbnail'] = str_replace('\\', '/', $fileInfo['thumbnail']);
                                $file['url'] = str_replace('\\', '/', $fileInfo['url']);
                                $file['type'] = $fileInfo['type'];
                                $file['name'] = $fileInfo['name'];
                                $file['insDate'] = $fileInfo['insDate'];
                                $file['fileId'] = $order_file->file_id;
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
            }
            $data = [
                'attachments' => $attachments
            ];
            return response()->json([
                'errorCode' => HttpCode::EC_OK,
                'errorMessage' => '',
                'data' => $data
            ]);
        } catch (Exception $exception) {
            logError($exception);
            return response()->json([
                'errorCode' => HttpCode::EC_APPLICATION_ERROR,
                'errorMessage' => $exception->getMessage()
            ]);
        }
    }

    public function updateOrderFiles(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'fileIds' => '',
                'orderId' => 'required',
                'orderStatus' => '', // Logic lay status cua order
                'type' => 'required', // Xoa hoac them moi tat ca
                'note' => ''
            ]);
            if ($validation->fails()) {
                return response()->json([
                    'errorCode' => HttpCode::EC_BAD_REQUEST,
                    'errorMessage' => $validation->messages()
                ]);
            } else {

                DB::beginTransaction();

                $fileIds = $request->get('fileIds', []);
                $orderId = $request->get('orderId', '');
                $orderStatus = $request->get('orderStatus', '');
                $type = $request->get('type', ''); // 1: insert - 2: delete
                $note = $request->get('note', ''); // 1: insert - 2: delete
                if ($fileIds != null && $orderId != null) {
                    if (1 == $type) {
                        foreach ($fileIds as $fileId) {
                            $orderFile = $this->getOrderFileRepos()->findFirstOrNew([]);
                            $orderFile->order_id = $orderId;
                            $orderFile->file_id = $fileId;
                            $orderFile->order_status = $orderStatus;
                            $orderFile->save();
                        }
                    } else if (2 == $type) {
                        foreach ($fileIds as $fileId) {
                            $orderFile = $this->getOrderFileRepos()->getOrderFileWithFileIdAndOrderId($fileId, $orderId);
                            if ($orderFile != null) {
                                $orderFile->delete();
                            }
                        }
                    }
                } else if (!empty($note)) {
                    $fs = $this->getOrderFileRepos()->getOrderFile($orderId, $orderStatus);
                    if (null != $fs && 0 < sizeof($fs)) {
                        $orderFile = $fs[0];
                        $orderFile->reason = $note;
                        $orderFile->save();
                    } else {
                        $orderFile = $this->getOrderFileRepos()->findFirstOrNew([]);
                        $orderFile->order_id = $orderId;
                        $orderFile->order_status = $orderStatus;
                        $orderFile->reason = $note;
                        $orderFile->save();
                    }
                }
                $orderFiles = $this->getOrderFileRepos()->getOrderFileWithOrderID($orderId);
                $data = $this->doProcessFileOrder($orderFiles);

                DB::commit();

                return response()->json([
                    'errorCode' => HttpCode::EC_OK,
                    'errorMessage' => '',
                    'data' => $data
                ]);
            }
        } catch (Exception $exception) {
            DB::rollBack();
            logError($exception);
            return response()->json([
                'errorCode' => HttpCode::EC_APPLICATION_ERROR,
                'errorMessage' => $exception->getMessage()
            ]);
        }
    }

    public function doProcessFileOrder($orderFiles)
    {
        $filesKhoiTao = [];
        $filesSanSang = [];
        $filesChoNhanHang = [];
        $filesDangVanChuyen = [];
        $filesHoanThanh = [];
        $filesHuy = [];
        $filesChoTaiXeXacNhan = [];
        $noteKhoiTao = '';
        $noteSanSang = '';
        $noteChoNhanHang = '';
        $noteDangVanChuyen = '';
        $noteHoanThanh = '';
        $noteHuy = '';
        $noteChoTaiXeXacNhan = '';

        foreach ($orderFiles as $key => $order_file) {
            $file = null;
            if ($order_file->file_id != null) {
                $fileInfo = app('App\Http\Controllers\Api\FileApiController')->getFile($order_file->file_id);
                if ($fileInfo != null) {
                    $file['urlThumbnail'] = str_replace('\\', '/', $fileInfo['thumbnail']);
                    $file['url'] = str_replace('\\', '/', $fileInfo['url']);
                    $file['type'] = $fileInfo['type'];
                    $file['name'] = $fileInfo['name'];
                    $file['insDate'] = $fileInfo['insDate'];
                    $file['fileId'] = $order_file->file_id;
                }
            }

            switch ($order_file->order_status) {
                case config("constant.KHOI_TAO"):
                    if (null != $file)
                        $filesKhoiTao[] = $file;
                    if (isset($order_file->reason) && !empty($order_file->reason)) {
                        $noteKhoiTao = $order_file->reason;
                    }
                    break;
                case config("constant.DANG_VAN_CHUYEN"):
                    if (null != $file)
                        $filesDangVanChuyen[] = $file;
                    if (isset($order_file->reason) && !empty($order_file->reason)) {
                        $noteDangVanChuyen = $order_file->reason;
                    }
                    break;
                case config("constant.HOAN_THANH"):
                    if (null != $file)
                        $filesHoanThanh[] = $file;
                    if (isset($order_file->reason) && !empty($order_file->reason)) {
                        $noteHoanThanh = $order_file->reason;
                    }
                    break;
                case config("constant.HUY"):
                    if (null != $file)
                        $filesHuy[] = $file;
                    if (isset($order_file->reason) && !empty($order_file->reason)) {
                        $noteHuy = $order_file->reason;
                    }
                    break;
            }
        }

        $attach = [[
            'status' => config("constant.KHOI_TAO"),
            'files' => $filesKhoiTao != null ? $filesKhoiTao : [],
            'note' => $noteKhoiTao
        ], [
            'status' => config("constant.DANG_VAN_CHUYEN"),
            'files' => $filesDangVanChuyen,
            'note' => $noteDangVanChuyen
        ], [
            'status' => config("constant.HOAN_THANH"),
            'files' => $filesHoanThanh,
            'note' => $noteHoanThanh
        ], [
            'status' => config("constant.HUY"),
            'files' => $filesHuy,
            'note' => $noteHuy
        ]];
        $data = [
            'attachments' => $attach
        ];
        return $data;
    }

    public function deleteOrder()
    {
        $id = Request::get('id');
        $order = Order::find($id);
        if (null == $order || empty($order)) {
            return response()->json([
                'errorCode' => HttpCode::EC_APPLICATION_ERROR,
                'errorMessage' => HttpCode::getMessageForCode(HttpCode::EC_APPLICATION_ERROR)
            ]);
        }
        $order->del_flag = 1;
        $order->save();
    }

    public function storeDB(Request $request)
    {
        try {
            DB::beginTransaction();
            $order = new Order;
            $isEdit = false;
            $param = Request::get('params');
            $orderOld = null;
            if ($param["ID"] != 0) {
                $order->id = $param['ID'];
                $isEdit = true;
                $order = $this->getOrderRepos()->getItemById($param['ID']);
                $orderOld = $order->replicate();
            } else {
                $order = $this->_defaultData($order);
                if (array_key_exists('orderCode', $param)) {
                    $order->order_code = $param['orderCode'];
                    $order->order_no = $param['orderCode'];
                }
            }

            $listDesLocation = $param['listLocationDestination'];
            $listArrLocation = $param['listLocationArrival'];
            $goodTypesChoosed = $param['goodTypesChoosed'];

            $order->location_destination_id = $listDesLocation[0]['location_id'];
            $order->ETD_date = $listDesLocation[0]['date'];
            $order->ETD_time = $listDesLocation[0]['time'];
            $order->location_arrival_id = $listArrLocation[0]['location_id'];
            $order->ETA_date = $listArrLocation[0]['date'];
            $order->ETA_time = $listArrLocation[0]['time'];
            $order->volume = $param['totalVolume'];
            $order->weight = $param['totalWeight'];
            $order->note = $param['Description'];
            $order->save();
            $listLocations = [];
            foreach ($listDesLocation as $des) {
                $listLocations[] = [
                    'type' => config('constant.DESTINATION'),
                    'date' => $des['date'],
                    'time' => $des['time'],
                    'date_reality' => null,
                    'time_reality' => null,
                    'note' => '',
                    'location_id' => $des['location_id']
                ];
            }

            foreach ($listArrLocation as $arr) {
                $listLocations[] = [
                    'type' => config('constant.ARRIVAL'),
                    'date' => $arr['date'],
                    'time' => $arr['time'],
                    'date_reality' => null,
                    'time_reality' => null,
                    'note' => '',
                    'location_id' => $arr['location_id']
                ];
            }

            $listGoodType = [];
            foreach ($goodTypesChoosed as $good) {
                $listGoodType[] = [
                    'goods_type_id' => $good['goodTypesId'],
                    'quantity' => $good['quantity'],
                    'goods_unit_id' => $good['goodUnitID'],
                    'insured_goods' => $good['insuredGoods'],
                    'note' => $good['note'],
                    'weight' => $good['weight'],
                    'volume' => $good['volume'],
                    'total_weight' => $good['weight'] * $good['quantity'],
                    'total_volume' => $good['volume'] * $good['quantity']
                ];
            }

            $order->listLocations()->detach();
            $order->listLocations()->sync($listLocations);

            $order->listGoods()->detach();
            $order->listGoods()->sync($listGoodType);

            if (!$isEdit) {
                if (!empty($order->id)) {
                    $this->_processCreateRelation($order);
                }
            }

            $this->_saveOrderFile($param, $order->id);
            // Notify all điều hành viên khi khởi tạo thành công đơn hàng
            $title = $order->customer_name . ' đặt lệnh đơn hàng ' . $order->order_code;
            // $message = Carbon::parse($order->ins_date)->format('d/m/Y H:i:s');
            $message = "Đơn hàng " . $order->order_code . " sẵn sàng vận chuyển.";
            app('App\Http\Controllers\Api\AlertLogApiController')->pushNotificationAdmin($title, $message, AppConstant::NOTIFICATION_SCREEN_ORDER, [], $order->id);

            //Trigger tạo bản ghi đồng bộ đối tác
            $this->getTPActionSyncRepos()->triggerActionSync($orderOld, $order);

            DB::commit();
        } catch (Exception $exception) {
            logError($exception);
            DB::rollBack();
        }
    }

    protected function _processCreateRelation($order, $isEdit = false)
    {
    }

    public function _defaultData($order)
    {
        $userId = Auth::User()->id;
        $customer = $this->getCustomerRepos()->getCustomerTypeByUserId($userId);
        $order->customer_id = $customer->id;
        $order->customer_name = $customer->full_name;
        $order->customer_mobile_no = $customer->mobile_no;
        $order->order_date = now()->toDateString("dd-mm-yyyy");
        $order->upd_date = now()->toDateString("dd-mm-yyyy");
        $code = app('App\Http\Controllers\Backend\SystemCodeConfigController')->generateSystemCode(config('constant.sc_order'), null, true);
        $order->status = config("constant.SAN_SANG");
        $order->precedence = config("constant.ORDER_PRECEDENCE_NORMAL");
        $order->order_code = $code;
        $order->order_no = $code;
        return $order;
    }

    public function getCodeConfig()
    {
        return app('App\Http\Controllers\Backend\SystemCodeConfigController')->getCodeConfig();
    }

    public function getCode()
    {
        $id = Request::get('id');
        if ($id == -1) {
            $id = null;
        }
        return app('App\Http\Controllers\Backend\SystemCodeConfigController')->generateSystemCode(config('constant.sc_order'), $id);
    }

    public function _saveOrderFile($data, $order_id)
    {
        $order_status_file_list = config("system.order_status_file");
        $this->getOrderFileRepos()->deleteWhere([
            'order_id' => $order_id
        ]);

        foreach ($order_status_file_list as $order_status) {

            if (!isset($data['order_file']))
                continue;
            if (!array_key_exists($order_status['id'], $data['order_file'])) {
                break;
            }
            $order_file = $data['order_file'][$order_status['id']];

            if (isset($order_file['file_id'])) {
                $file_id_list = explode(';', $order_file['file_id']);

                foreach ($file_id_list as $file_id) {

                    $orderFileEntity = $this->getOrderFileRepos()->findFirstOrNew([]);

                    $orderFileEntity->order_id = $order_id;
                    $orderFileEntity->order_status = $order_status['id'];
                    $orderFileEntity->file_id = $file_id;
                    if (isset($order_file['reason']))
                        $orderFileEntity->reason = $order_file['reason'];

                    $orderFileEntity->save();
                    app('App\Http\Controllers\Api\FileApiController')->moveFileFromTmpToMedia($orderFileEntity->file_id, 'orders');
                }
            } else {
                if ($order_status['id'] != config("constant.KHOI_TAO")) {
                    $orderFileEntity = $this->getOrderFileRepos()->findFirstOrNew([]);
                    $orderFileEntity->order_id = $order_id;
                    $orderFileEntity->order_status = $order_status['id'];
                    $orderFileEntity->reason = $order_file['reason'];

                    $orderFileEntity->save();
                }
            }
        }
    }

    //Cap nhat so hoa don tu app
    public function updateBillNo(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'orderId' => '',
                'billNo' => '',
            ]);
            if ($validation->fails()) {
                return response()->json([
                    'errorCode' => HttpCode::EC_BAD_REQUEST,
                    'errorMessage' => $validation->messages()
                ]);
            } else {
                DB::beginTransaction();
                $order = $this->getOrderRepos()->getItemById($request['orderId']);
                if (null == $order || empty($order)) {
                    return response()->json([
                        'errorCode' => HttpCode::EC_BAD_REQUEST,
                        'errorMessage' => HttpCode::getMessageForCode(4)
                    ]);
                }
                $order->bill_no = $request['billNo'];
                $order->save();
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
            logError($exception . '-' . json_encode($request));
            DB::rollBack();
            return response()->json([
                'errorCode' => HttpCode::EC_APPLICATION_ERROR,
                'errorMessage' => HttpCode::getMessageForCode(HttpCode::EC_APPLICATION_ERROR)
            ]);
        }
    }

    /**
     * Cập nhật riêng từng trường thông tin Đơn hàng
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * Created by ptly on 2020.09.11
     */
    public function updateOrderField(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'orderId' => 'required',
                'fieldName' => 'required',
                'fieldValue' => 'required',
            ]);
            if ($validation->fails()) {
                return response()->json([
                    'errorCode' => HttpCode::EC_BAD_REQUEST,
                    'errorMessage' => $validation->messages()
                ]);
            } else {
                DB::beginTransaction();
                $order = $this->getOrderRepos()->getModelById($request['orderId']);
                if (null == $order || empty($order)) {
                    return response()->json([
                        'errorCode' => HttpCode::EC_BAD_REQUEST,
                        'errorMessage' => HttpCode::getMessageForCode(4)
                    ]);
                }
                $fieldName = $request['fieldName'];
                $order->{$fieldName} = $request['fieldValue'];
                $order->save();
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
            logError($exception . '-' . json_encode($request));
            DB::rollBack();
            return response()->json([
                'errorCode' => HttpCode::EC_APPLICATION_ERROR,
                'errorMessage' => HttpCode::getMessageForCode(HttpCode::EC_APPLICATION_ERROR)
            ]);
        }
    }

    // Tích hợp sync orders với 3P
    // Insert vào đơn hàng con
    public function orders(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'orders' => 'required|array',
                'orders.*.order_no' => 'sometimes|nullable|distinct',
                'orders.*.bill_no' => 'sometimes|nullable|distinct',
                'orders.*.order_date' => 'required|date_format:Y-m-d',
                'orders.*.location_destination' => 'required',
                'orders.*.contact_name_destination' => '',
                'orders.*.contact_mobile_no_destination' => '',
                'orders.*.time_destination' => 'required|date_format:Y-m-d H:i',
                'orders.*.location_arrival' => 'required',
                'orders.*.contact_name_arrival' => '',
                'orders.*.contact_mobile_no_arrival' => '',
                'orders.*.time_arrival' => 'required|date_format:Y-m-d H:i',
                'goods' => [
                    // TODO: Bổ sung logic, nhập free hàng hóa
                    'code' => '',
                    'quantity' => ''
                ],
                'orders.*.total_weight' => '',
                'orders.*.total_volume' => '',
                'orders.*.total_quantity' => '',
                'orders.*.cod_amount' => '',
                'orders.*.bill_print_url' => '',
                'orders.*.note' => ''
            ]);
            if ($validation->fails()) {
                return response()->json([
                    'errorCode' => HttpCode::EC_BAD_REQUEST,
                    'errorMessage' => $validation->messages()
                ]);
            } else {
                // TODO: Cần validate dữ liệu với Database

                // Lưu dữ liệu đơn hàng: trạng thái sẵn sàng, thông báo cho điều hành
                $result = $this->saveOrder3P($request);
                return $result;
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            logError($exception);
            return response()->json([
                'errorCode' => HttpCode::EC_APPLICATION_ERROR,
                'errorMessage' => $exception->getMessage()
            ]);
        }
    }

    public function saveOrder3P(Request $request)
    {
        $orders = $request['orders'];
        DB::beginTransaction();
        try {
            // Dấu & trong for để giữ cho item updated trong array
            foreach ($orders as &$item) {
                $order = new Order();
                $order = $this->_defaultData3P($order, $item['order_no']);
                $order->order_no = $item['order_no'];
                $order->bill_no = isset($item['bill_no']) ? $item['bill_no'] : '';
                $order->order_date = $item['order_date'];
                $order->bill_print_url = env('1MG_PRINT_URL') . $item['order_no'];
                $order->weight = isset($item['total_weight']) ? $item['total_weight'] : '';
                $order->volume = isset($item['total_volume']) ? $item['total_volume'] : '';
                $order->quantity = isset($item['total_quantity']) ? $item['total_quantity'] : '';
                $order->cod_amount = isset($item['cod_amount']) ? $item['cod_amount'] : '';

                // Địa điểm
                $order->contact_name_destination = isset($item['contact_name_destination']) ? $item['contact_name_destination'] : '';
                $order->contact_mobile_no_destination = isset($item['contact_mobile_no_destination']) ? $item['contact_mobile_no_destination'] : '';
                $listLocations = [];
                $locationDes = $this->doLocationInput($item['location_destination']);
                $timeDes = date($item['time_destination']);
                $order->location_destination_id = $locationDes->id;
                $order->ETD_date = explode(" ", $timeDes)[0];
                $order->ETD_time = explode(" ", $timeDes)[1];
                $listLocations[] = [
                    'type' => config('constant.DESTINATION'),
                    'date' => explode(" ", $timeDes)[0],
                    'time' => explode(" ", $timeDes)[1],
                    'date_reality' => null,
                    'time_reality' => null,
                    'note' => '',
                    'location_id' => $locationDes->id
                ];

                $order->contact_name_arrival = isset($item['contact_name_arrival']) ? $item['contact_name_arrival'] : '';
                $order->contact_mobile_no_arrival = isset($item['contact_mobile_no_arrival']) ? $item['contact_mobile_no_arrival'] : '';
                $locationArr = $this->doLocationInput($item['location_arrival']);
                $timeArr = date($item['time_arrival']);
                $order->location_arrival_id = $locationArr->id;
                $order->ETA_date = explode(" ", $timeArr)[0];
                $order->ETA_time = explode(" ", $timeArr)[1];
                $listLocations[] = [
                    'type' => config('constant.ARRIVAL'),
                    'date' => explode(" ", $timeArr)[0],
                    'time' => explode(" ", $timeArr)[1],
                    'date_reality' => null,
                    'time_reality' => null,
                    'note' => '',
                    'location_id' => $locationArr->id
                ];

                $order->note = isset($item['note']) ? $item['note'] : '';
                $order->save();
                $order->listLocations()->detach();
                $order->listLocations()->sync($listLocations);
                $this->_processCreateRelation($order);
                $item['order_code'] = $order->order_code;
                $item['amount'] = $order->amount;

                // Hàng hóa

                // Notify all điều hành viên khi khởi tạo thành công đơn hàng
                $title = $order->customer_name . ' đặt lệnh đơn hàng ' . $order->order_code;
                // $message = Carbon::parse($order->ins_date)->format('d/m/Y H:i:s');
                $message = "Đơn hàng " . $order->order_code . " sẵn sàng vận chuyển.";
                app('App\Http\Controllers\Api\AlertLogApiController')->pushNotificationAdmin($title, $message, AppConstant::NOTIFICATION_SCREEN_ORDER, [], $order->id);

                //Tạo DHKH
                $this->createOrderCustomer3P($order);
            }
            DB::commit();
            return response()->json([
                'errorCode' => HttpCode::EC_OK,
                'errorMessage' => '',
                'data' => $orders
            ]);
        } catch (\Exception $exception) {
            DB::rollBack();
            logError($exception);
            return response()->json([
                'errorCode' => HttpCode::EC_APPLICATION_ERROR,
                'errorMessage' => $exception->getMessage()
            ]);
        }
    }

    public function _defaultData3P($order, $orderNo)
    {
        $userId = Auth::User()->id;
        $customer = $this->getCustomerRepos()->getCustomerTypeByUserId($userId);
        $order->customer_id = $customer->id;
        $order->customer_name = $customer->full_name;
        $order->customer_mobile_no = $customer->mobile_no;
        $code = app('App\Http\Controllers\Backend\SystemCodeConfigController')->generateSystemCode(config('constant.sc_order'), null, true);
        $order->status = config("constant.SAN_SANG");
        $order->precedence = config("constant.ORDER_PRECEDENCE_NORMAL");
        // Cách tạo mã hệ thống của 1MG: 1MG_Time_số đơn hàng_mã hệ thống
        $date = date('md');
        $order->order_code = '1MG_' . $date . '_' . $orderNo . '_' . $code;
        return $order;
    }

    public function doLocationInput($address)
    {
        // TODO: Kiểm tra thêm định dạng của địa điểm theo cấu trúc: địa chỉ, xã/phường, quận/huyện, tỉnh/thành phố

        $location = $this->getLocationRepos()->findAddress($address);
        if (!isset($location)) {
            $location = $this->getLocationRepos()->findFirstOrNew([]);
            $code = app('App\Http\Controllers\Backend\SystemCodeConfigController')->generateSystemCode(config('constant.sc_location'), null, true);
            $location->code = $code;
            $location->title = $address;
            $location->full_address = $address;
            $location->address = $address;
            $location->province_id = '';
            $location->district_id = '';
            $location->ward_id = '';
            $location->address_auto_code = ' -  - ';
            $location->latitude = '';
            $location->longitude = '';
            $location->save();
            return $location;
        }
        return $location;
    }

    public function tpCancel(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'orders' => 'required|array',
                'orders.*.order_no' => 'required|distinct',
                'orders.*.note' => ''
            ]);
            if ($validation->fails()) {
                return response()->json([
                    'errorCode' => HttpCode::EC_BAD_REQUEST,
                    'errorMessage' => $validation->messages()
                ]);
            } else {
                $orders = $request['orders'];
                $totalSuccess = 0;
                $totalFailed = 0;
                DB::beginTransaction();
                foreach ($orders as &$item) {
                    $orderArr = $this->getOrderRepos()->getOrderByOrderNo($item['order_no']);
                    // Chỉ có 1 đơn hàng, ở trạng thái sẵn sàng (chưa xử lý) mới có thể hủy
                    if (isset($orderArr) && 1 == sizeof($orderArr) && $orderArr[0]->status == config('constant.SAN_SANG')) {
                        $totalSuccess++;
                        $obj = $orderArr[0];
                        $obj->status = config('constant.HUY');
                        $obj->save();
                        $item['status'] = config('constant.HUY');

                        // Lưu reason trong bảng order_file
                        if (!empty($item['note'])) {
                            $orderFile = new OrderFile();
                            $orderFile->order_id = $obj->id;
                            $orderFile->order_status = config('constant.HUY');
                            $orderFile->reason = $item['note'];
                            $orderFile->save();
                        }
                    } else {
                        $totalFailed++;
                    }
                }
                DB::commit();
                return response()->json([
                    'errorCode' => HttpCode::EC_OK,
                    'errorMessage' => '',
                    'data' => [
                        'success' => $totalSuccess,
                        'failed' => $totalFailed,
                        'orders' => $orders
                    ]
                ]);
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            logError($exception);
            return response()->json([
                'errorCode' => HttpCode::EC_APPLICATION_ERROR,
                'errorMessage' => $exception->getMessage()
            ]);
        }
    }

    public function createOrderCustomer3P($order)
    {
        $orderCustomer = $this->getOrderCustomerRepos()->findFirstOrNew([]);
        $systemCode = app('App\Http\Controllers\Backend\SystemCodeConfigController')->generateSystemCode(config('constant.sc_order_customer'), null, true);
        $orderCustomer->code = $systemCode;

        $orderCustomer->name = $order->order_code;
        $orderCustomer->order_no = $order->order_no;
        $orderCustomer->order_date = $order->order_date;
        $orderCustomer->customer_id = $order->customer_id;
        $orderCustomer->customer_name = $order->customer_name;
        $orderCustomer->customer_mobile_no = $order->customer_mobile_no;
        $orderCustomer->ETD_date = $order->ETD_date;
        $orderCustomer->ETD_time = $order->ETD_time;
        $orderCustomer->ETA_date = $order->ETA_date;
        $orderCustomer->ETA_time = $order->ETA_time;
        $orderCustomer->location_destination_id = $order->location_destination_id;
        $orderCustomer->location_arrival_id = $order->location_arrival_id;
        $orderCustomer->amount = $order->amount;
        $orderCustomer->weight = $order->weight;
        $orderCustomer->volume = $order->volume;

        if ($order->status == config('constant.HOAN_THANH')) {
            $orderCustomer->status = config('constant.status_complete');
            $orderCustomer->ETA_date_reality = $order->ETA_date_reality;
            $orderCustomer->ETA_time_reality = $order->ETA_time_reality;
        } elseif ($order->status == config('constant.HUY')) {
            $orderCustomer->status = config('constant.status_cancel');
            $orderCustomer->ETA_date_reality = $order->ETA_date_reality;
            $orderCustomer->ETA_time_reality = $order->ETA_time_reality;
        } else {
            $orderCustomer->status = config('constant.status_incomplete');
        }
        $orderCustomer->save();

        // Cập nhật đơn hàng vào DHKH
        $order->order_customer_id = $orderCustomer->id;
        $order->save();
    }

    // End: Tích hợp 3P
    //

    public function deleteGoods(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'ids' => 'required|array',
            ]);
            if ($validation->fails()) {
                return response()->json([
                    'errorCode' => HttpCode::EC_BAD_REQUEST,
                    'errorMessage' => $validation->messages()
                ]);
            } else {

                DB::beginTransaction();
                $ids = $request["ids"];
                OrderGoods::whereIn('id', $ids)->delete();


                DB::commit();
                return response()->json([
                    'errorCode' => HttpCode::EC_OK,
                    'errorMessage' => ''
                ]);
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            logError($exception);
            return response()->json([
                'errorCode' => HttpCode::EC_APPLICATION_ERROR,
                'errorMessage' => $exception->getMessage()
            ]);
        }
    }

    public function saveGoods(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'order_id' => 'required',
                'goods_type_id' => 'required',
                'goods_unit_id' => 'required',
                'insured_goods' => 'required',
                'quantity' => 'required',
                'weight' => 'required',
                'volume' => 'required',
                'total_weight' => 'required',
                'total_volume' => 'required',
            ]);
            if ($validation->fails()) {
                return response()->json([
                    'errorCode' => HttpCode::EC_BAD_REQUEST,
                    'errorMessage' => $validation->messages()
                ]);
            } else {

                DB::beginTransaction();

                $input = [
                    'order_id' => $request["order_id"],
                    'goods_type_id' => $request["goods_type_id"],
                    'goods_unit_id' => $request["goods_unit_id"],
                    'insured_goods' => $request["insured_goods"],
                    'quantity' => $request["quantity"],
                    'weight' => $request["weight"],
                    'volume' => $request["volume"],
                    'total_weight' => $request["total_weight"],
                    'total_volume' => $request["total_volume"],
                    'note' => array_key_exists('note', $request->all()) ? $request["note"] : '',
                ];
                if (empty($request["id"])) {
                    OrderGoods::create($input);
                } else {
                    OrderGoods::where('id', $request["id"])
                        ->update($input);
                }

                DB::commit();
                return response()->json([
                    'errorCode' => HttpCode::EC_OK,
                    'errorMessage' => ''
                ]);
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            logError($exception);
            return response()->json([
                'errorCode' => HttpCode::EC_APPLICATION_ERROR,
                'errorMessage' => $exception->getMessage()
            ]);
        }
    }
}
