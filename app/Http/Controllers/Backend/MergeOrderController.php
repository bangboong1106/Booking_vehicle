<?php

namespace App\Http\Controllers\Backend;

use App\Common\HttpCode;
use App\Http\Controllers\Base\BackendController;
use App\Repositories\ColumnConfigRepository;
use App\Repositories\MergeOrderRepository;
use App\Repositories\OrderRepository;
use App\Repositories\RoutesRepository;
use App\Repositories\VehicleRepository;
use App\Services\RouteService;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use QrCode;

class MergeOrderController extends BackendController
{
    protected $_orderRepository;
    protected $_routesRepository;
    protected $_columnConfigRepository;
    protected $_vehicleRepository;
    protected $_routeService;

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

    public function __construct(
        MergeOrderRepository $mergeOrderRepository,
        OrderRepository $orderRepository,
        RoutesRepository $routesRepository,
        ColumnConfigRepository $columnConfigRepository,
        VehicleRepository $vehicleRepository,
        RouteService $routeService
    )
    {
        parent::__construct();
        set_time_limit(0);
        $this->setRepository($mergeOrderRepository);
        $this->setOrderRepository($orderRepository);
        $this->setBackUrlDefault('merge-order.index');
        $this->setConfirmRoute('merge-order.confirm');
        $this->setMenu('order');
        $this->setTitle(trans('models.merge_order.name'));

        $this->setRoutesRepository($routesRepository);
        $this->setColumnConfigRepository($columnConfigRepository);
        $this->setVehicleRepository($vehicleRepository);
        $this->setRouteService($routeService);
    }

    protected function _prepareIndex()
    {
        $userId = Auth::User()->id;
        $configList = $this->getColumnConfigRepository()->getConfigList($userId, config('constant.cf_merge_order'));
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
            'content' => $this->render('backend.merge_order.merge_order_content')->render(),
        ];
    }

    /**
     * TH1: Gh??p v??o chuy???n c?? s???n
     * TH2: T???o chuy???n m???i
     * @param $orderIds
     * @param $routeId
     * @param $vehicleId
     * @param $driverId
     * @return string|void
     */
    public function addOrderToRoute($orderIds, $routeId, $vehicleId, $driverId)
    {
        $message = "";

        //Check DH ???? c?? chuy???n ch??a
        $routeList = $this->getRoutesRepository()->getRoutesByOrders($orderIds);
        if ($routeList && count($routeList) > 0) {
            $message = "????n h??ng ???? thu???c chuy???n : " . implode(",", $routeList->pluck("route_code")->toArray());
            return $message;
        }

        if (empty($routeId) && (empty($vehicleId) || empty($driverId))) {
            $message = "Ch??a nh???p Xe - T??i x???";
            return $message;
        }

        $orderList = $this->getOrderRepository()->getOrdersByIds($orderIds);
        if ($orderList && count($orderList) > 0) {
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

                        //C???p nh???t xe v?? t??i x??? v??o ????n
                        app('App\Http\Controllers\Backend\OrderController')->_processOrderFromRoute(
                            $order->status <= config('constant.SAN_SANG') ? 1 : 2,
                            $order,
                            $vehicleId,
                            $driverId,
                            $routeId
                        );
                    }
                    $this->getRouteService()->updateRouteInfo($route);
                }
            } else {
                foreach ($orderList as $order) {
                    //C???p nh???t xe v?? t??i x??? v??o ????n
                    app('App\Http\Controllers\Backend\OrderController')->_processOrderFromRoute(
                        $order->status <= config('constant.SAN_SANG') ? 1 : 2,
                        $order,
                        $vehicleId,
                        $driverId,
                        null
                    );
                }

                //T???o m???i chuy???n
                $vehicle = $this->getVehicleRepository()->getItemById($vehicleId);
                $this->getRouteService()->createNewRoute($orderList, $vehicleId, $driverId, $vehicle ? $vehicle->group_id : null);
            }
        }
        return $message;
    }

    //API gh??p h??ng
    function mergeOrderSave()
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

            logError($e);
            return response()->json([
                'errorCode' => HttpCode::EC_APPLICATION_ERROR,
                'errorMessage' => $e->getMessage(),
            ]);
        }
    }

    //API l???y chuy???n theo xe
    public function getRouteByVehicles()
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
            logError($e);
            return response()->json([
                'errorCode' => HttpCode::EC_APPLICATION_ERROR,
                'errorMessage' => $e->getMessage(),
                'data' => $data
            ]);
        }
    }

    // L???y xe v?? t??i x??? m???c ?????nh
    public function default()
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
            logError($e);
            return response()->json([
                'errorCode' => HttpCode::EC_APPLICATION_ERROR,
                'errorMessage' => $e->getMessage(),
            ]);
        }
    }
}
