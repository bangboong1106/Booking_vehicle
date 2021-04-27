<?php

namespace App\Http\Controllers\Api\ApiAppClient;

use App\Common\AppConstant;
use App\Common\HttpCode;
use App\Http\Controllers\Base\ClientApiController;
use App\Model\Entities\Order;
use App\Model\Entities\OrderCustomerReview;
use App\Repositories\CustomerRepository;
use App\Repositories\DriverRepository;
use App\Repositories\FileRepository;
use App\Repositories\LocationRepository;
use App\Repositories\OrderFileRepository;
use App\Repositories\OrderHistoryRepository;
use App\Repositories\Client\OrderClientRepository;
use App\Repositories\TPActionSyncRepository;
use App\Repositories\VehicleRepository;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Input;
use JWTAuth;
use Exception;
use Validator;

class OrderApiController extends ClientApiController
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

    public function __construct(
        OrderClientRepository $orderRepository,
        FileRepository $fileRepository,
        OrderFileRepository $orderFileRepository,
        DriverRepository $driverRepository,
        OrderHistoryRepository $orderHistoryRepository,
        CustomerRepository $customerRepository,
        VehicleRepository $vehicleRepos,
        LocationRepository $locationRepository,
        TPActionSyncRepository $tpActionSyncRepository
    ) {
        parent::__construct($customerRepository);
        $this->setRepository($orderRepository);
        $this->setFileRepos($fileRepository);
        $this->setOrderFileRepos($orderFileRepository);
        $this->setDriverRepos($driverRepository);
        $this->setOrderHistoryRepos($orderHistoryRepository);
        $this->setCustomerRepos($customerRepository);
        $this->setVehicleRepos($vehicleRepos);
        $this->setLocationRepos($locationRepository);
        $this->setTPActionSyncRepos($tpActionSyncRepository);
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

    public function delete(Request $request)
    {
        $id = Request::get('id');
        $order = Order::find($id);
        if (null == $order || empty($order)) {
            return response()->json([
                'errorCode' => HttpCode::EC_APPLICATION_ERROR,
                'errorMessage' => HttpCode::getMessageForCode(HttpCode::EC_APPLICATION_ERROR)
            ]);
        } else {
            if ($order->status != config('constant.SAN_SANG')) {
                return response()->json([
                    'errorCode' => HttpCode::EC_OK,
                    'errorMessage' => 'Không thể xoá đơn hàng đang được xử lý. Vui lòng tải lại dữ liệu.'
                ]);
            } else {
                $order->del_flag = 1;
                $order->save();
                return response()->json([
                    'errorCode' => HttpCode::EC_OK,
                    'errorMessage' => ""
                ]);
            }
        }
    }

    public function save(Request $request)
    {
        try {
            DB::beginTransaction();
            $order = new Order;
            $isEdit = false;
            $param = Request::get('params');
            $orderOld = null;

            logError(json_encode($param));

            if ($param["ID"] != 0) {
                $order->id = $param['ID'];
                $isEdit = true;
                $order = $this->getRepository()->getItemById($param['ID']);
                $orderOld = $order->replicate();
            } else {
                $order = $this->_defaultData($order);
            }
            if (array_key_exists('orderCode', $param)) {
                $order->order_code = $param['orderCode'];
                $order->order_no = $param['orderCode'];
            }
            $param['id'] = $order->id;
            $param['status'] = $order->status;
            $param['customer_id'] = $order->customer_id;
            $param['order_code'] = $order->order_code;
            $param['order_no'] = $order->order_no;
            $param['precedence'] = $order->precedence;
            $param['locationDestinations'] = $param['listLocationDestination'];
            $param['locationArrivals'] = $param['listLocationArrival'];

            $validator = $this->getRepository()->getValidator();
            $isValidate = $param["ID"] != 0 ? $validator->validateUpdate($param) : $validator->validateCreate($param);
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


            $listDesLocation = $param['listLocationDestination'];
            $listArrLocation = $param['listLocationArrival'];
            $selectedGoodsItems = array_key_exists('goodTypesChooosed', $param) ? $param['goodTypesChoosed']
                : (isset($param['chosenGoodsItems']) ? $param['chosenGoodsItems'] : []);

            $order->vin_no = array_key_exists('vin_no', $param) ? $param['vin_no'] : '';
            $order->model_no = array_key_exists('model_no', $param) ? $param['model_no'] : '';
            $order->location_destination_id = $listDesLocation[0]['location_id'];
            $order->ETD_date = $listDesLocation[0]['date'];
            $order->ETD_time = $listDesLocation[0]['time'];
            $order->location_arrival_id = $listArrLocation[0]['location_id'];
            $order->ETA_date = $listArrLocation[0]['date'];
            $order->ETA_time = $listArrLocation[0]['time'];
            $order->note = $param['Description'];
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

            $listGoodsItems = [];
            $volume = 0;
            $weight = 0;
            foreach ($selectedGoodsItems as $goodsItem) {
                $listGoodsItems[] = [
                    'goods_type_id' => array_key_exists('goodTypesId', $goodsItem) ? $goodsItem['goodTypesId'] : $goodsItem['goodsTypeId'],
                    'quantity' => $goodsItem['quantity'],
                    'goods_unit_id' => $goodsItem['goodUnitID'],
                    'insured_goods' => $goodsItem['insuredGoods'],
                    'note' => $goodsItem['note'],
                    'weight' => $goodsItem['weight'],
                    'volume' => $goodsItem['volume'],
                    'total_weight' => $goodsItem['weight'] * $goodsItem['quantity'],
                    'total_volume' => $goodsItem['volume'] * $goodsItem['quantity']
                ];
                $weight += $goodsItem['weight'] * $goodsItem['quantity'];
                $volume += $goodsItem['volume'] * $goodsItem['quantity'];
            }
            $order->volume = isset($param['totalVolume']) && $param['totalVolume'] != 0 ? $param['totalVolume'] : $order->volume = $volume;
            $order->weight = isset($param['totalWeight']) && $param['totalWeight'] != 0 ? $param['totalWeight'] : $order->weight = $weight;

            $order->save();

            $order->listLocations()->detach();
            $order->listLocations()->sync($listLocations);

            $order->listGoods()->detach();
            $order->listGoods()->sync($listGoodsItems);

            if (!$isEdit) {
                if (!empty($order->id)) {
                    $this->_processCreateRelation($order);
                }
            }

            $this->_saveOrderFile($param, $order->id);
            // Notify all điều hành viên khi khởi tạo thành công đơn hàng
            $title = $order->customer_name . ' đặt lệnh đơn hàng ' . $order->order_code;
            $message = "Đơn hàng " . $order->order_code . " sẵn sàng vận chuyển.";
            app('App\Http\Controllers\Api\AlertLogApiController')->pushNotificationAdmin($title, $message, AppConstant::NOTIFICATION_SCREEN_ORDER, [], $order->id);

            //Trigger tạo bản ghi đồng bộ đối tác
            $this->getTPActionSyncRepos()->triggerActionSync($orderOld, $order);

            DB::commit();
            return response()->json([
                'errorCode' => HttpCode::EC_OK,
                'errorMessage' => '',
            ]);
        } catch (Exception $exception) {
            logError($exception);
            DB::rollBack();
            return response()->json(
                [
                    'errorCode' => HttpCode::EC_APPLICATION_ERROR,
                    'errorMessage' => HttpCode::getMessageForCode(HttpCode::EC_APPLICATION_ERROR)
                ]
            );
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

    //Lấy danh sách đơn hàng của khách hàng
    public function event()
    {
        $start = Request::get('start');
        $end = Request::get('end');

        $userId = Auth::User()->id;
        $customer = $this->getCustomerRepos()->getCustomerTypeByUserId($userId);
        $events = [];
        if ($customer) {
            $customerID = $customer->id;
            $query = DB::table('orders')
                ->leftJoin('drivers', 'drivers.id', '=', 'orders.primary_driver_id')
                ->leftJoin('vehicle', 'vehicle.id', '=', 'orders.vehicle_id')
                ->where('orders.customer_id', '=', $customerID)
                ->where('orders.del_flag', '=', '0')
                ->where(function ($q) use ($start, $end) {
                    $q->where([
                        ['orders.ETD_date', '>=', $start],
                        ['orders.ETA_date', '<=', $end],
                    ])
                        ->orWhere([
                            ['orders.ETD_date', '<=', $start],
                            ['orders.ETA_date', '>=', $end],
                        ])
                        ->orWhere([
                            ['orders.ETD_date', '<=', $start],
                            ['orders.ETA_date', '>', $start],
                            ['orders.ETA_date', '<=', $end],
                        ])
                        ->orWhere([
                            ['orders.ETD_date', '>=', $start],
                            ['orders.ETD_date', '<', $end],
                            ['orders.ETA_date', '>=', $end],
                        ]);
                });

            $events = $query->distinct()->get([
                'orders.id as id',
                'vehicle.id as resourceId',
                'orders.order_code as title',
                'orders.status as status',
                'orders.id as orderId',
                DB::raw('(CASE 
            WHEN orders.status = 1 THEN "#f8f9fa"
            WHEN orders.status = 2 THEN "#6c757d"
            WHEN orders.status = 3 THEN "#9d5508" 
            WHEN orders.status = 4 THEN "rgb(103, 139, 251)" 
            WHEN orders.status = 5 THEN "#28a745" 
            WHEN orders.status = 6 THEN "#343a40"
            WHEN orders.status = 7 THEN "#aa315b"
                                ELSE "" END) AS color'),
                DB::raw('concat(orders.ETA_date,\' \',orders.ETA_time) as end'),
                DB::raw('concat(orders.ETD_date,\' \',orders.ETD_time) as start'),
            ]);
        }
        return json_encode($events);
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
        foreach ($order_status_file_list as $order_status) {
            if (!isset($data['order_file'])) {
                continue;
            }
            if (!array_key_exists($order_status['id'], $data['order_file'])) {
                continue;
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

    //Lấy danh sách thông tin chi tiết đơn hàng cho client
    public function detailInfo(Request $request)
    {
        $order = $this->getRepository()->getOrderByID(Request::get('id'));
        return response()->json([
            'errorCode' => HttpCode::EC_OK,
            'errorMessage' => '',
            'data' => $order
        ]);
    }

    //Lấy thông tin trạng thái đơn hàng
    public function tracking()
    {
        $order = $this->getRepository()->getOrderByID(Request::get('id'));
        $response = [
            'ready' => false,
            'wait_delivery' => false,
            'received' => false,
            'shipped' => false,
            'delivered' => false,
            'order' => $order
        ];
        switch ($order->status) {
            case config('constant.SAN_SANG'):
            case config('constant.TAI_XE_XAC_NHAN'):
                $response = [
                    'ready' => true,
                    'wait_delivery' => false,
                    'received' => false,
                    'shipped' => false,
                    'delivered' => false,
                    'order' => $order
                ];
                break;
            case config('constant.CHO_NHAN_HANG'):
                $response = [
                    'ready' => true,
                    'wait_delivery' => true,
                    'received' => false,
                    'shipped' => false,
                    'delivered' => false,
                    'order' => $order
                ];
                break;
            case config('constant.DANG_VAN_CHUYEN'):
                $response = [
                    'ready' => true,
                    'wait_delivery' => true,
                    'received' => true,
                    'shipped' => true,
                    'delivered' => false,
                    'order' => $order
                ];
                break;
            case config('constant.HOAN_THANH'):
                $response = [
                    'ready' => true,
                    'wait_delivery' => true,
                    'received' => true,
                    'shipped' => true,
                    'delivered' => true,
                    'order' => $order
                ];
                break;
            case config('constant.HUY'):
                $response = [
                    'ready' => false,
                    'wait_delivery' => false,
                    'received' => false,
                    'shipped' => false,
                    'delivered' => false,
                    'canceled' => true,
                    'order' => $order
                ];
                break;
        }
        return response()->json($response);
    }

    // API lấy lịch sử sửa đổi của bản ghi
    // CreatedBy ptly 16/07/2020
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
    // CreatedBy ptly 16/07/2020
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

    // API lay thong tin review đơn hàng
    public function reviewInfo(Request $request)
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
            $id = $request->get('id');
            $data = $this->getRepository()->getReviewInfo($id);
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

    // API thực hiện review đơn hàng
    // Neu đơn hàng đã review => ko cho phép review lại
    public function doReview(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'id' => 'required',
                'point' => 'required',
                'description' => '',
                'fileIds' => ''
            ]);
            if ($validation->fails()) {
                return response()->json([
                    'errorCode' => HttpCode::EC_BAD_REQUEST,
                    'errorMessage' => $validation->messages()
                ]);
            }
            $id = $request->get('id', 0);
            $order = $this->getRepository()->getItemById($id);
            // Neu đơn hàng đã review => ko cho phép review lại
            if (isset($order) && null == $order->order_review_id) {
                $fileIds = $request->get('fileIds', []);
                $point = $request->get('point', '5');
                $description = $request->get('description', '');
                if ($fileIds != null && 0 < sizeof($fileIds)) {
                    foreach ($fileIds as $fileId) {
                        $orderFile = $this->getOrderFileRepos()->findFirstOrNew([]);
                        $orderFile->order_id = $id;
                        $orderFile->file_id = $fileId;
                        $orderFile->order_status = config("constant.FILE_REVIEW_ORDER_TYPE");
                        $orderFile->save();
                    }
                }
                $review = new OrderCustomerReview();
                $review->point = $point;
                $review->description = $description != null ? $description : '';
                $review->order_id = $id;
                $review->save();

                $order->order_review_id = $review->id;
                $order->save();
                return response()->json([
                    'errorCode' => HttpCode::EC_OK,
                    'errorMessage' => '',
                    'data' => $review
                ]);
            } else {
                return response()->json([
                    'errorCode' => HttpCode::EC_BAD_REQUEST,
                    'errorMessage' => 'Dữ liệu không hợp lệ'
                ]);
            }
        } catch (Exception $exception) {
            logError($exception);
            return response()->json(
                [
                    'errorCode' => HttpCode::EC_APPLICATION_ERROR,
                    'errorMessage' => $exception->getMessage()
                ]
            );
        }
    }
}
