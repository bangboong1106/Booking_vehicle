<?php

namespace App\Http\Controllers\Backend;

use App\Common\AppConstant;
use App\Common\HttpCode;
use App\Http\Controllers\Base\BackendController;
use App\Repositories\AdminUserInfoRepository;
use App\Repositories\ColumnConfigRepository;
use App\Repositories\OrderRepository;
use App\Repositories\PartnerOrderRepository;
use App\Repositories\RoutesRepository;
use App\Repositories\VehicleRepository;
use App\Services\NotificationService;
use App\Services\RouteService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use QrCode;
use Validator;

class PartnerOrderController extends BackendController
{
    protected $_orderRepository;
    protected $_routesRepository;
    protected $_columnConfigRepository;
    protected $_vehicleRepository;
    protected $_adminUserRepository;
    protected $_routeService;
    protected $_notificationService;

    /**
     * @return mixed
     */
    public function getOrderRepository()
    {
        return $this->_orderRepository;
    }

    /**
     * @param mixed $orderRepository
     */
    public function setOrderRepository($orderRepository): void
    {
        $this->_orderRepository = $orderRepository;
    }

    /**
     * @return mixed
     */
    public function getRoutesRepository()
    {
        return $this->_routesRepository;
    }

    /**
     * @param mixed $routesRepository
     */
    public function setRoutesRepository($routesRepository): void
    {
        $this->_routesRepository = $routesRepository;
    }

    /**
     * @return mixed
     */
    public function getColumnConfigRepository()
    {
        return $this->_columnConfigRepository;
    }

    /**
     * @param mixed $columnConfigRepository
     */
    public function setColumnConfigRepository($columnConfigRepository): void
    {
        $this->_columnConfigRepository = $columnConfigRepository;
    }

    /**
     * @return mixed
     */
    public function getVehicleRepository()
    {
        return $this->_vehicleRepository;
    }

    /**
     * @param mixed $vehicleRepository
     */
    public function setVehicleRepository($vehicleRepository): void
    {
        $this->_vehicleRepository = $vehicleRepository;
    }

    /**
     * @return mixed
     */
    public function getAdminUserRepository()
    {
        return $this->_adminUserRepository;
    }

    /**
     * @param mixed $adminUserRepository
     */
    public function setAdminUserRepository($adminUserRepository): void
    {
        $this->_adminUserRepository = $adminUserRepository;
    }

    /**
     * @return mixed
     */
    public function getRouteService()
    {
        return $this->_routeService;
    }

    /**
     * @param mixed $routeService
     */
    public function setRouteService($routeService): void
    {
        $this->_routeService = $routeService;
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
        PartnerOrderRepository $partnerOrderRepository,
        OrderRepository $orderRepository,
        RoutesRepository $routesRepository,
        ColumnConfigRepository $columnConfigRepository,
        VehicleRepository $vehicleRepository,
        AdminUserInfoRepository $adminUserInfoRepository,
        RouteService $routeService,
        NotificationService $notificationService
    )
    {
        parent::__construct();
        set_time_limit(0);
        $this->setRepository($partnerOrderRepository);
        $this->setOrderRepository($orderRepository);
        $this->setBackUrlDefault('partner-order.index');
        $this->setConfirmRoute('partner-order.confirm');
        $this->setMenu('partner_order');
        $this->setTitle(trans('models.partner_order.name'));

        $this->setRoutesRepository($routesRepository);
        $this->setColumnConfigRepository($columnConfigRepository);
        $this->setVehicleRepository($vehicleRepository);
        $this->setAdminUserRepository($adminUserInfoRepository);
        $this->setRouteService($routeService);
        $this->setNotificationService($notificationService);
    }

    protected function _prepareIndex()
    {
        $userId = Auth::User()->id;
        $configList = $this->getColumnConfigRepository()->getConfigList($userId, config('constant.cf_order'));
        $this->setViewData([
            'configList' => $configList["configList"],
            'sort_field' => $configList["sort_field"],
            'sort_type' => $configList["sort_type"],
            'page_size' => $configList["page_size"]
        ]);
    }

    public function mergeOrderForm()
    {
        $orderIds = empty(Request::get('orderIds')) ? null : explode(',', Request::get('orderIds'));
        $orders = $this->getOrderRepository()->getOrdersByIds($orderIds);
        $vehicleList = $this->getOrderRepository()->getVehicleForOrders($orderIds);
        $this->setViewData([
            'orders' => $orders,
            'vehicleList' => $vehicleList,
        ]);
        return [
            'content' => $this->render('backend.partner_order.merge_order_content')->render(),
        ];
    }

    /**
     * TH1: Ghép vào chuyến có sẵn
     * TH2: Tạo chuyến mới
     * @param $orderIds
     * @param $routeId
     * @param $vehicleId
     * @param $driverId
     * @return string|void
     */
    public function addOrderToRoute($orderIds, $routeId, $vehicleId, $driverId)
    {
        $message = "";

        //Check chứa đơn hủy
        $validHasOrderCancel = $this->getOrderRepository()->validHasOrderCancel($orderIds);
        if ($validHasOrderCancel) {
            $message = "Tồn tại đơn hàng đã bị hủy";
            return $message;
        }

        //Check DH đã có chuyến chưa
        if (empty($routeId) && (empty($vehicleId) || empty($driverId))) {
            $message = "Chưa chọn Chuyến hoặc chưa nhập Xe - Tài xế";
            return $message;
        }

        $orderList = $this->getOrderRepository()->getOrdersByIds($orderIds);
        if ($orderList && count($orderList) > 0) {
            $routeIds = array_column($orderList->toArray(), 'route_id');

            if (!empty($routeId)) {
                $route = $this->getRoutesRepository()->getItemById($routeId);

                if ($route) {
                    if (empty($vehicleId)) {
                        $vehicleId = $route->vehicle_id;
                    }
                    if (empty($driverId)) {
                        $driverId = $route->driver_id;
                    }

                    foreach ($orderList as $order) {

                        //Cập nhật xe và tài xế vào đơn
                        app('App\Http\Controllers\Backend\OrderController')->_processOrderFromRoute(
                            $order->status <= config('constant.SAN_SANG') ? 1 : 2,
                            $order,
                            $vehicleId,
                            $driverId,
                            $routeId
                        );

                        $type = 2;
                        if ($order->status > config('constant.SAN_SANG')
                            && ($order->vehicle_id != $vehicleId || $order->primary_driver_id != $driverId))
                            $type = 6;
                        $this->getNotificationService()->notifyPartnerToC20($type, ['order_id' => $order->id, 'order_code' => $order->order_code]);

                    }
                    $this->getRouteService()->updateRouteInfo($route);
                }
            } else {
                foreach ($orderList as $order) {
                    //Cập nhật xe và tài xế vào đơn
                    app('App\Http\Controllers\Backend\OrderController')->_processOrderFromRoute(
                        $order->status <= config('constant.SAN_SANG') ? 1 : 2,
                        $order,
                        $vehicleId,
                        $driverId,
                        null
                    );
                    $type = 2;
                    if ($order->status > config('constant.SAN_SANG')
                        && ($order->vehicle_id != $vehicleId || $order->primary_driver_id != $driverId))
                        $type = 6;
                    $this->getNotificationService()->notifyPartnerToC20($type, ['order_id' => $order->id, 'order_code' => $order->order_code]);
                }

                //Tạo mới chuyến
                $vehicle = $this->getVehicleRepository()->getItemById($vehicleId);
                $this->getRouteService()->createNewRoute($orderList, $vehicleId, $driverId, $vehicle ? $vehicle->group_id : null);
            }

            //Cập nhật lại thông tin chuyến cũ
            $routeCurrentList = $this->getRoutesRepository()->getItemsByIds($routeIds);
            foreach ($routeCurrentList as $routeCurrent) {
                $this->getRouteService()->updateRouteOld($routeCurrent);
            }
        }
        return $message;
    }

    //API ghép hàng
    function mergeOrderSave(Request $request)
    {
        try {
            DB::beginTransaction();

            $orderIds = empty(Request::get('order_ids')) ? null : explode(',', Request::get('order_ids'));
            $routeId = empty(Request::get('route_id')) ? null : Request::get('route_id');
            $vehicleId = empty(Request::get('vehicle_id')) ? null : Request::get('vehicle_id');
            $driverId = empty(Request::get('driver_id')) ? null : Request::get('driver_id');

            $message = $this->addOrderToRoute($orderIds, $routeId, $vehicleId, $driverId);

            DB::commit();

            return response()->json([
                'errorCode' => empty($message) ? HttpCode::EC_OK : HttpCode::EC_APPLICATION_WARNING,
                'errorMessage' => $message,
            ]);
        } catch (Exception $e) {
            DB::rollBack();

            logError($e . ' - Data : ' . $request);
            return response()->json([
                'errorCode' => HttpCode::EC_APPLICATION_ERROR,
                'errorMessage' => $e->getMessage(),
            ]);
        }
    }

    //API lấy chuyến theo xe
    public function getRouteByVehicles(Request $request)
    {
        $data = [];
        try {

            $vehicleIds = empty(Request::get('vehicleIds')) ? null : explode(',', Request::get('vehicleIds'));
            $routesList = $this->getRoutesRepository()->getRouteByVehicles($vehicleIds);
            $data = [
                'routesList' => $routesList
            ];

            return response()->json([
                'errorCode' => HttpCode::EC_OK,
                'errorMessage' => '',
                'data' => $data
            ]);
        } catch (Exception $e) {
            logError($e . ' - Data : ' . $request);
            return response()->json([
                'errorCode' => HttpCode::EC_APPLICATION_ERROR,
                'errorMessage' => $e->getMessage(),
                'data' => $data
            ]);
        }
    }

    // Lấy xe và tài xế mặc định
    public function default(Request $request)
    {
        try {
            $orderIds = empty(Request::get('order_ids')) ? null : explode(',', Request::get('order_ids'));
            $result = $this->getOrderRepository()->getDefaultVehicleAndDriverByOrderIDs($orderIds);
            return response()->json([
                'errorCode' => HttpCode::EC_OK,
                'errorMessage' => '',
                'data' => $result
            ]);
        } catch (Exception $e) {
            logError($e . ' - Data : ' . $request);
            return response()->json([
                'errorCode' => HttpCode::EC_APPLICATION_ERROR,
                'errorMessage' => $e->getMessage(),
            ]);
        }
    }

    public function acceptOrderSave(Request $request)
    {
        try {
            DB::beginTransaction();
            $message = '';

            $orderIds = empty(Request::get('order_ids')) ? null : explode(',', Request::get('order_ids'));

            $valid = $this->getOrderRepository()->validHasPartnerAccept($orderIds);
            if ($valid) {
                return response()->json([
                    'errorCode' => HttpCode::EC_APPLICATION_WARNING,
                    'errorMessage' => 'Tồn tại đơn hàng đối tác vận tải đã xác nhận.',
                ]);
            }

            $orderList = $this->getOrderRepository()->getItemsByIds($orderIds);

            foreach ($orderList as $order) {
                $order->status_partner = config('constant.PARTNER_XAC_NHAN');
                $order->status = config('constant.SAN_SANG');
                $order->save();
                $this->getNotificationService()->notifyPartnerToC20(1, ['order_id' => $order->id, 'order_code' => $order->order_code]);
            }

            DB::commit();

            return response()->json([
                'errorCode' => empty($message) ? HttpCode::EC_OK : HttpCode::EC_APPLICATION_WARNING,
                'errorMessage' => $message,
            ]);
        } catch (Exception $e) {
            DB::rollBack();

            logError($e . ' - Data : ' . $request);
            return response()->json([
                'errorCode' => HttpCode::EC_APPLICATION_ERROR,
                'errorMessage' => $e->getMessage(),
            ]);
        }
    }

    public function requestEditOrderSave(Request $request)
    {
        try {
            DB::beginTransaction();
            $message = '';

            $orderIds = empty(Request::get('order_ids')) ? null : explode(',', Request::get('order_ids'));
            $reason = empty(Request::get('reason')) ? null : Request::get('reason');

            $valid = $this->getOrderRepository()->validHasPartnerAccept($orderIds);
            if ($valid) {
                return response()->json([
                    'errorCode' => HttpCode::EC_APPLICATION_WARNING,
                    'errorMessage' => 'Tồn tại đơn hàng bạn đã xác nhận.',
                ]);
            }

            $orderList = $this->getOrderRepository()->getItemsByIds($orderIds);

            foreach ($orderList as $order) {
                $order->status_partner = config('constant.PARTNER_YEU_CAU_SUA');
                $order->reason = $reason;
                $order->save();
                $this->getNotificationService()->notifyPartnerToC20(3, ['order_id' => $order->id, 'order_code' => $order->order_code]);
            }

            DB::commit();

            return response()->json([
                'errorCode' => empty($message) ? HttpCode::EC_OK : HttpCode::EC_APPLICATION_WARNING,
                'errorMessage' => $message,
            ]);
        } catch (Exception $e) {
            DB::rollBack();

            logError($e . ' - Data : ' . $request);
            return response()->json([
                'errorCode' => HttpCode::EC_APPLICATION_ERROR,
                'errorMessage' => $e->getMessage(),
            ]);
        }
    }

    public function cancelOrderSave(Request $request)
    {
        try {
            DB::beginTransaction();
            $message = '';

            $orderIds = empty(Request::get('order_ids')) ? null : explode(',', Request::get('order_ids'));
            $orderList = $this->getOrderRepository()->getItemsByIds($orderIds);

            foreach ($orderList as $order) {
                $order->status_partner = config('constant.PARTNER_HUY');
                $order->status = config('constant.HUY');
                $order->save();

                if ($order->status >= config('constant.SAN_SANG')) {
                    //Xử lý chuyến
                    $this->getRouteService()->_processRouteFromOrder(
                        2,
                        $order,
                        $order->route_id,
                        $order->vehicle_id,
                        $order->primary_driver_id,
                        $order
                    );
                }

                $this->getNotificationService()->notifyPartnerToC20(4, ['order_id' => $order->id, 'order_code' => $order->order_code]);
            }

            DB::commit();

            return response()->json([
                'errorCode' => empty($message) ? HttpCode::EC_OK : HttpCode::EC_APPLICATION_WARNING,
                'errorMessage' => $message,
            ]);
        } catch (Exception $e) {
            DB::rollBack();

            logError($e . ' - Data : ' . $request);
            return response()->json([
                'errorCode' => HttpCode::EC_APPLICATION_ERROR,
                'errorMessage' => $e->getMessage(),
            ]);
        }
    }

    public function completeOrderSave(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'order_ids' => 'required',
                'ETD_date_reality' => 'required',
                'ETD_time_reality' => 'required',
                'ETA_date_reality' => 'required',
                'ETA_time_reality' => 'required',
            ], [
                'required' => 'Thời gian nhận , trả thực tế là bắt buộc'
            ]);
            if ($validation->fails()) {
                return response()->json([
                    'errorCode' => HttpCode::EC_BAD_REQUEST,
                    'errorMessage' => $validation->messages()
                ]);
            }

            $orderIds = explode(',', Request::get('order_ids'));
            $ETD_date_reality = AppConstant::convertDate(Request::get('ETD_date_reality'));
            $ETD_time_reality = AppConstant::convertTime(Request::get('ETD_time_reality'));
            $ETA_date_reality = AppConstant::convertDate(Request::get('ETA_date_reality'));
            $ETA_time_reality = AppConstant::convertTime(Request::get('ETA_time_reality'));

            $message = '';
            if (!AppConstant::isDate2GreatDate1($ETD_time_reality . ' ' . $ETD_date_reality, $ETA_time_reality . ' ' . $ETA_date_reality)) {
                $message = 'Thời gian trả hàng thực tế phải lớn hơn thời gian nhận hàng thực tế';
                return response()->json([
                    'errorCode' => HttpCode::EC_APPLICATION_WARNING,
                    'errorMessage' => $message
                ]);
            }

            if (!$this->getOrderRepository()->validHasRoute($orderIds)) {
                $message = 'Tồn tại đơn hàng chưa có chuyến.';
                return response()->json([
                    'errorCode' => HttpCode::EC_APPLICATION_WARNING,
                    'errorMessage' => $message
                ]);
            }

            DB::beginTransaction();

            $orderList = $this->getOrderRepository()->getItemsByIds($orderIds);

            foreach ($orderList as $order) {
                $order->status = config('constant.HOAN_THANH');
                $order->ETD_date_reality = $ETD_date_reality;
                $order->ETD_time_reality = $ETD_time_reality;
                $order->ETA_date_reality = $ETA_date_reality;
                $order->ETA_time_reality = $ETA_time_reality;
                $order->save();

                //Xử lý chuyến
                $this->getRouteService()->_processRouteFromOrder(
                    2,
                    $order,
                    $order->route_id,
                    $order->vehicle_id,
                    $order->primary_driver_id,
                    $order
                );

                $this->getNotificationService()->notifyPartnerToC20(5, ['order_id' => $order->id, 'order_code' => $order->order_code]);
            }

            DB::commit();

            return response()->json([
                'errorCode' => empty($message) ? HttpCode::EC_OK : HttpCode::EC_APPLICATION_WARNING,
                'errorMessage' => $message,
            ]);
        } catch (Exception $e) {
            DB::rollBack();

            logError($e . ' - Data : ' . $request);
            return response()->json([
                'errorCode' => HttpCode::EC_APPLICATION_ERROR,
                'errorMessage' => $e->getMessage(),
            ]);
        }
    }
}
