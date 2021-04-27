<?php

namespace App\Http\Controllers\Api\ApiAppManagement;

use App\Common\HttpCode;
use App\Http\Controllers\Base\ManagementApiController;
use App\Repositories\CustomerRepository;
use App\Repositories\DriverRepository;
use App\Repositories\FileRepository;
use App\Repositories\LocationRepository;
use App\Repositories\Management\OrderManagementRepository;
use App\Repositories\OrderCustomerRepository;
use App\Repositories\OrderFileRepository;
use App\Repositories\OrderHistoryRepository;
use App\Repositories\RouteCostRepository;
use App\Repositories\RoutesRepository;
use App\Repositories\TPActionSyncRepository;
use App\Repositories\VehicleRepository;
use App\Services\OrderService;
use App\Services\RouteService;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Input;
use JWTAuth;
use Exception;
use Validator;

class OrderApiController extends ManagementApiController
{
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
    protected $routeService;
    protected $orderService;

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
     * @return mixed
     */
    public function getRouteService()
    {
        return $this->routeService;
    }

    /**
     * @param mixed $routeService
     */
    public function setRouteService($routeService): void
    {
        $this->routeService = $routeService;
    }

    /**
     * @return mixed
     */
    public function getOrderService()
    {
        return $this->orderService;
    }

    /**
     * @param mixed $orderService
     */
    public function setOrderService($orderService): void
    {
        $this->orderService = $orderService;
    }

    public function __construct(
        OrderManagementRepository $orderRepository,
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
        RouteService $routeService,
        OrderService $orderService
    )
    {
        parent::__construct();
        $this->setRepository($orderRepository);
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
        $this->setRouteService($routeService);
        $this->setOrderService($orderService);
    }

    // API lưu thông tin
    // CreatedBy nlhoang 27/05/2020
    public function save(Request $request)
    {
        $params = $this->_getParams();

        try {
            DB::beginTransaction();
            $this->_setFormData($params);
            $orderOld = null;
            if (isset($params['id'])) {
                $orderOld = $this->getRepository()->getItemById($params['id']);
            }

            $entity = $this->_findEntityForStore();
            $entity->order_no = $entity->code;
            $entity->is_collected_documents = config("constant.no");
            $entity->status_collected_documents = config("constant.CHUA_THU_DU");
            $entity->commission_type = config("constant.TONG_TIEN_HOA_HONG");
            $entity->commission_value = 0;

            $ETD_date_reality = isset($params['ETD_date_reality']) ? $params['ETD_date_reality'] : null;
            $ETD_time_reality = isset($params['ETD_time_reality']) ? $params['ETD_time_reality'] : null;
            $ETA_date_reality = isset($params['ETA_date_reality']) ? $params['ETA_date_reality'] : null;
            $ETA_time_reality = isset($params['ETA_time_reality']) ? $params['ETA_time_reality'] : null;

            $entity->locationDestinations[] = [
                'location_id' => $params['location_destination_id'],
                'date' => $params['ETD_date'],
                'time' => $params['ETD_time'],
                'date_reality' => $ETD_date_reality,
                'time_reality' => $ETD_time_reality
            ];
            $entity->locationArrivals[] = [
                'location_id' => $params['location_arrival_id'],
                'date' => $params['ETA_date'],
                'time' => $params['ETA_time'],
                'date_reality' => $ETA_date_reality,
                'time_reality' => $ETA_time_reality
            ];

            $params['locationDestinations'] = [
                [
                    'location_id' => $params['location_destination_id'],
                    'date' => $params['ETD_date'],
                    'time' => $params['ETD_time'],
                    'date_reality' => $ETD_date_reality,
                    'time_reality' => $ETD_time_reality
                ]
            ];
            $params['locationArrivals'] = [
                [
                    'location_id' => $params['location_arrival_id'],
                    'date' => $params['ETA_date'],
                    'time' => $params['ETA_time'],
                    'date_reality' => $ETA_date_reality,
                    'time_reality' => $ETA_time_reality
                ]
            ];

            $validator = $this->getRepository()->getValidator();
            $isValidate = isset($params['id']) ? $validator->validateUpdate($params) : $validator->validateCreate($params);
            if (!$isValidate) {
                $errors = $this->getRepository()->getValidator()->errorsBag();
                $validators = [];
                foreach ($errors->messages() as $key => $message) {
                    $validators[] = [
                        'field' => $key,
                        'message' => Arr::get($message, 0)
                    ];
                }
                return response()->json([
                    'errorCode' => HttpCode::EC_APPLICATION_ERROR,
                    'errorMessage' => $validators,
                    'data' => null
                ]);
            }
            //Valid trang thai don
            $error = app('App\Http\Controllers\Backend\OrderController')->_validStatusFollowField($params);
            if (!empty($error)) {
                return response()->json([
                    'errorCode' => HttpCode::EC_APPLICATION_ERROR,
                    'errorMessage' => ['status' => $error],
                    'data' => null
                ]);
            }
            if (isset($params['goods'])) { // ptly: tính tổng trọng lượng, thể tích của đơn hàng
                $goods = $params['goods'];
                $volume = 0;
                $weight = 0;
                if (!empty($goods)) {
                    foreach ($goods as $goodsItem) {
                        $volume += $goodsItem['total_volume'];
                        $weight += $goodsItem['total_weight'];
                    }
                }
                $entity->weight = $weight;
                $entity->volume = $volume;
            }

            if (!isset($params['id']))
                $entity->source_create = config("constant.SOURCE_CREATE_ORDER_APP_MANAGE");

            $entity->save();
            app('App\Http\Controllers\Backend\OrderController')->_saveRelations($entity);
            $data = [
                'route_create' => 1,
                'vehicle_id' => isset($params['vehicle_id']) ? $params['vehicle_id'] : null,
                'primary_driver_id' => isset($params['primary_driver_id']) ? $params['primary_driver_id'] : null,
                'secondary_driver_id' => isset($params['secondary_driver_id']) ? $params['secondary_driver_id'] : null,
                'route_id' => isset($params['route_id']) ? $params['route_id'] : null,
            ];

            app('App\Http\Controllers\Backend\OrderController')->_processCreateRelation($entity, $data, isset($params['id']) ? true : false, $orderOld);

            DB::commit();
            return response()->json([
                'errorCode' => HttpCode::EC_OK,
                'errorMessage' => '',
                'data' => null
            ]);
        } catch (Exception $exception) {
            DB::rollBack();
            logError($exception);
            return response()->json(
                [
                    'errorCode' => HttpCode::EC_APPLICATION_ERROR,
                    'errorMessage' => HttpCode::getMessageForCode(HttpCode::EC_APPLICATION_ERROR)
                ]
            );
        }
    }

    // API lấy danh sách đối tượng
    // CreatedBy nlhoang 20/05/2020
    public function getOrderForRouteList(Request $request)
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
                $drivers = $this->getRepository()->getOrderForRouteList($request);
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

    // API lấy lịch sử sửa đổi của bản ghi
    // CreatedBy nlhoang 03/06/2020
    public function history(Request $request)
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
            }
            $id = $request->get('id', 0);
            $data = $this->getRepository()->getHistory($id);
            return response()->json([
                'errorCode' => HttpCode::EC_OK,
                'errorMessage' => '',
                'data' => $data
            ]);
        } catch (Exception $exception) {
            logError($exception);
            return response()->json(
                [
                    'errorCode' => HttpCode::EC_APPLICATION_ERROR,
                    'errorMessage' => HttpCode::getMessageForCode(HttpCode::EC_APPLICATION_ERROR)
                ]
            );
        }
    }


    // API lấy thông tin lộ trình đơn hàng
    // CreatedBy nlhoang 03/06/2020
    public function route(Request $request)
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
            }
            $id = $request->get('id', 0);
            $data = $this->getRepository()->getRoute($id);
            return response()->json([
                'errorCode' => HttpCode::EC_OK,
                'errorMessage' => '',
                'data' => $data
            ]);
        } catch (Exception $exception) {
            logError($exception);
            return response()->json(
                [
                    'errorCode' => HttpCode::EC_APPLICATION_ERROR,
                    'errorMessage' => HttpCode::getMessageForCode(HttpCode::EC_APPLICATION_ERROR)
                ]
            );
        }
    }

    // API lấy thông tin đơn hàng trên bảng điều khiển
    // CreatedBy nlhoang 03/06/2020
    public function control(Request $request)
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
            }
            $fromDate = $request->get('fromDate', 0);
            $toDate = $request->get('toDate', 0);
            $vehicleTeamIDs = $request->get('vehicleTeams');
            $vehicleIDs = $request->get('vehicles');
            $vehicleGroupIDs = $request->get('vehicleGroups');
            $customerIDs = $request->get('customers');
            $data = $this->getRepository()->getOrderOnControlBoard($fromDate, $toDate, $vehicleTeamIDs, $vehicleIDs, $vehicleGroupIDs, $customerIDs);
            return response()->json([
                'errorCode' => HttpCode::EC_OK,
                'errorMessage' => '',
                'data' => $data
            ]);
        } catch (Exception $exception) {
            logError($exception);
            return response()->json(
                [
                    'errorCode' => HttpCode::EC_APPLICATION_ERROR,
                    'errorMessage' => HttpCode::getMessageForCode(HttpCode::EC_APPLICATION_ERROR)
                ]
            );
        }
    }

    // API lấy thông tin đơn hàng trên lệnh vận chuyển
    // CreatedBy nlhoang 04/06/2020
    public function order(Request $request)
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
            }
            $fromDate = $request->get('fromDate', 0);
            $toDate = $request->get('toDate', 0);
            $data = $this->getRepository()->getOrderOnOrderBoard($fromDate, $toDate);
            return response()->json([
                'errorCode' => HttpCode::EC_OK,
                'errorMessage' => '',
                'data' => $data
            ]);
        } catch (Exception $exception) {
            logError($exception);
            return response()->json(
                [
                    'errorCode' => HttpCode::EC_APPLICATION_ERROR,
                    'errorMessage' => HttpCode::getMessageForCode(HttpCode::EC_APPLICATION_ERROR)
                ]
            );
        }
    }

    // Ghi đè API lấy chi tiết bản ghi => Chỉ lấy đơn hàng có khách hàng thuộc nhóm mà user quản lý
    // CreatedBy ptly 22.07.2020
    public function detail(Request $request)
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
            }
            $id = $request->get('id', 0);
            $userId = Auth::User()->id;
            $customerDetail = $this->getRepository()->getDataByID($id, $userId);
            return response()->json([
                'errorCode' => HttpCode::EC_OK,
                'errorMessage' => '',
                'data' => $customerDetail
            ]);
        } catch (Exception $exception) {
            logError($exception);
            return response()->json(
                [
                    'errorCode' => HttpCode::EC_APPLICATION_ERROR,
                    'errorMessage' => HttpCode::getMessageForCode(HttpCode::EC_APPLICATION_ERROR)
                ]
            );
        }
    }

    public function _deleteRelations($order_id)
    {
        if ($order_id) {
            $this->getOrderHistoryRepos()->deleteWhere([
                'order_id' => $order_id
            ]);

            //delete order_file
            $orderFiles = $this->getOrderFileRepos()->getOrderFileWithOrderID($order_id);
            if ($orderFiles != null) {
                foreach ($orderFiles as $orderFileEntity) {
                    $fileEntity = $this->getFileRepos()->getFileWithID($orderFileEntity->file_id);
                    if ($fileEntity != null) $fileEntity->delete();
                    $orderFileEntity->delete();
                }
            }

            $order = $this->getRepository()->getItemById($order_id);

            //Xử lý chuyến khi xóa đơn
            $this->getRouteService()->_processRouteFromOrderDelete($order);

        }
    }

    public function splitOrder(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'order_id' => 'required',
                'order_split_list' => 'required',
            ]);
            if ($validation->fails()) {
                return response()->json([
                    'errorCode' => HttpCode::EC_BAD_REQUEST,
                    'errorMessage' => $validation->messages()
                ]);
            }

            $orderId = Request::get('order_id');
            $dataOrders = Request::get('order_split_list');
            if ($dataOrders) {
                foreach ($dataOrders as &$dataOrder) {
                    $dataGoodsList = $dataOrder['goods_list'];
                    unset($dataOrder['goods_list']);
                    foreach ($dataGoodsList as $dataGoods) {
                        $dataOrder['goods_list'][$dataGoods['goods_type_id']] = $dataGoods['quantity'];
                    }
                }
            }

            $order = $this->getRepository()->getItemById($orderId);
            if (!$order) {
                return response()->json([
                    'errorCode' => HttpCode::EC_BAD_REQUEST,
                    'errorMessage' => 'Không tìm thấy đơn hàng vận tải'
                ]);
            }
            $validMessage = $this->getOrderService()->validSplitOrder($order, $dataOrders);
            if (!empty($validMessage)) {
                return response()->json([
                    'errorCode' => HttpCode::EC_BAD_REQUEST,
                    'errorMessage' => $validMessage
                ]);
            }

            DB::beginTransaction();

            $this->getOrderService()->splitOrder($order, $dataOrders, config('constant.SOURCE_CREATE_C20_ORDER_SPLIT'));

            DB::commit();

            return response()->json([
                'errorCode' => HttpCode::EC_OK,
                'errorMessage' => '',
            ]);
        } catch (Exception $exception) {
            DB::rollBack();

            logError($exception . '- Data : ' . json_encode($request));
            return response()->json([
                'errorCode' => HttpCode::EC_APPLICATION_ERROR,
                'errorMessage' => $exception->getMessage(),
            ]);
        }
    }

    public function mergeOrder(Request $request)
    {
        try {

            $orderIds = empty(Request::get('order_ids')) ? null : explode(',', Request::get('order_ids'));
            DB::beginTransaction();

            $orders = $this->getRepository()->getItemsByIds($orderIds);

            $this->getOrderService()->mergeOrder($orders, config('constant.SOURCE_CREATE_C20_ORDER_MERGE'));

            DB::commit();

            return response()->json([
                'errorCode' => HttpCode::EC_OK,
                'errorMessage' => '',
            ]);
        } catch (Exception $exception) {
            DB::rollBack();

            logError($exception . '- Data : ' . json_encode($request));
            return response()->json([
                'errorCode' => HttpCode::EC_APPLICATION_ERROR,
                'errorMessage' => $exception->getMessage(),
            ]);
        }
    }

    function updatePartnerOrder(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'order_ids' => 'required',
                'partner_id' => 'required',
            ], [
                'order_ids.required' => 'Bạn chưa chọn đơn hàng',
                'partner_id.required' => 'Đối tác vận tải là bắt buộc'
            ]);
            if ($validation->fails()) {
                return response()->json([
                    'errorCode' => HttpCode::EC_BAD_REQUEST,
                    'errorMessage' => $validation->messages()
                ]);
            }

            DB::beginTransaction();

            $orderIds = empty(Request::get('order_ids')) ? null : explode(',', Request::get('order_ids'));
            $vehicleId = empty(Request::get('vehicle_id')) ? null : Request::get('vehicle_id');
            $driverId = empty(Request::get('driver_id')) ? null : Request::get('driver_id');
            $partnerId = empty(Request::get('partner_id')) ? null : Request::get('partner_id');
            $mergerRoute = empty(Request::get('merge_route')) ? false : Request::get('merge_route');

            $message = app('App\Http\Controllers\Backend\OrderController')->addPartnerToOrder($orderIds, $partnerId, $vehicleId, $driverId, $mergerRoute);

            DB::commit();

            return response()->json([
                'errorCode' => empty($message) ? HttpCode::EC_OK : HttpCode::EC_APPLICATION_WARNING,
                'errorMessage' => $message,
            ]);
        } catch (Exception $e) {
            DB::rollBack();

            logError($e . ' - Data ' . json_encode($request));
            return response()->json([
                'errorCode' => HttpCode::EC_APPLICATION_ERROR,
                'errorMessage' => $e->getMessage(),
            ]);
        }
    }


    public function files(Request $request)
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

                DB::commit();

                $data = $this->getRepository()->getFiles($orderId);
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
}
