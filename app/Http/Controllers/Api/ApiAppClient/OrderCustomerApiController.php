<?php

namespace App\Http\Controllers\Api\ApiAppClient;

use App\Common\AppConstant;
use App\Common\HttpCode;
use App\Http\Controllers\Base\ClientApiController;
use App\Model\Entities\OrderCustomer;
use App\Model\Entities\OrderCustomerGoods;
use App\Model\Entities\OrderCustomerHistory;
use App\Repositories\AdminUserInfoRepository;
use App\Repositories\Client\OrderCustomerClientRepository;
use App\Repositories\CustomerRepository;
use App\Repositories\DistrictRepository;
use App\Repositories\GoodsTypeRepository;
use App\Repositories\LocationRepository;
use App\Services\NotificationService;
use App\Services\OrderCustomerService;
use DateTime;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Input;
use Exception;
use Validator;

class OrderCustomerApiController extends ClientApiController
{

    protected $customerRepos;
    protected $orderCustomerRepos;
    protected $locationRepos;
    protected $districtRepos;
    protected $goodsTypeRepos;
    protected $orderCustomerService;
    protected $notificationService;
    protected $adminUserRepository;

    public function getCustomerRepos()
    {
        return $this->customerRepos;
    }

    public function setCustomerRepos($customerRepos)
    {
        $this->customerRepos = $customerRepos;
    }

    public function getLocationRepos()
    {
        return $this->locationRepos;
    }

    public function setLocationRepos($locationRepos)
    {
        $this->locationRepos = $locationRepos;
    }

    public function getDistrictRepos()
    {
        return $this->districtRepos;
    }

    public function setDistrictRepos($districtRepos)
    {
        $this->districtRepos = $districtRepos;
    }

    /**
     * @return mixed
     */
    public function getGoodsTypeRepos()
    {
        return $this->goodsTypeRepos;
    }

    /**
     * @param mixed $goodsTypeRepos
     */
    public function setGoodsTypeRepos($goodsTypeRepos): void
    {
        $this->goodsTypeRepos = $goodsTypeRepos;
    }

    /**
     * @return mixed
     */
    public function getOrderCustomerService()
    {
        return $this->orderCustomerService;
    }

    /**
     * @param mixed $orderCustomerService
     */
    public function setOrderCustomerService($orderCustomerService): void
    {
        $this->orderCustomerService = $orderCustomerService;
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

    /**
     * @return mixed
     */
    public function getAdminUserRepository()
    {
        return $this->adminUserRepository;
    }

    /**
     * @param mixed $adminUserRepository
     */
    public function setAdminUserRepository($adminUserRepository): void
    {
        $this->adminUserRepository = $adminUserRepository;
    }

    public function __construct(
        CustomerRepository $customerRepository,
        OrderCustomerClientRepository $orderCustomerRepository,
        LocationRepository $locationRepository,
        DistrictRepository $districtRepository,
        GoodsTypeRepository $goodsTypeRepository,
        OrderCustomerService $orderCustomerService,
        NotificationService $notificationService,
        AdminUserInfoRepository $adminUserInfoRepository
    )
    {
        parent::__construct($customerRepository);
        $this->setRepository($orderCustomerRepository);
        $this->setCustomerRepos($customerRepository);
        $this->setLocationRepos($locationRepository);
        $this->setDistrictRepos($districtRepository);
        $this->setGoodsTypeRepos($goodsTypeRepository);
        $this->setOrderCustomerService($orderCustomerService);
        $this->setNotificationService($notificationService);
        $this->setAdminUserRepository($adminUserInfoRepository);
    }

    //Lấy danh sách thông tin chi tiết đơn hàng cho client
    public function order(Request $request)
    {
        $orders = $this->getRepository()->getOrdersByID(Request::get('id'));
        return response()->json([
            'errorCode' => HttpCode::EC_OK,
            'errorMessage' => '',
            'data' => $orders
        ]);
    }

    //Lưu thông tin trạng thái đơn hàng
    public function saveStatus(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'id' => 'required',
                'status' => 'required'
            ]);
            if ($validation->fails()) {
                return response()->json([
                    'errorCode' => HttpCode::EC_BAD_REQUEST,
                    'errorMessage' => $validation->messages()
                ]);
            }
            $status = Request::get('status');
            $note = Request::get('note');

            DB::beginTransaction();
            $entity = $this->getRepository()->getItemById(Request::get('id'));
            $entity->status = $status;
            $entity->reason = $note;

            $entity->save();

            OrderCustomerHistory::insert([
                'order_customer_id' => $entity->id,
                'status' => $entity->status,
                'reason' => $entity->reason
            ]);

            $type = 0;
            switch ($entity->status) {
                case config('constant.ORDER_CUSTOMER_STATUS.CHU_HANG_XAC_NHAN'):
                    $type = 1;
                    break;
                case config('constant.ORDER_CUSTOMER_STATUS.CHU_HANG_YEU_CAU_SUA'):
                    $type = 2;
                    break;
                case config('constant.ORDER_CUSTOMER_STATUS.CHU_HANG_HUY'):
                    $type = 3;
                    break;
            }

            $client = $this->getCustomerRepository()->getItemById($entity->client_id);
            $userIds = $client ? [$client->user_id] : [];
            $this->getNotificationService()->notifyCustomerToClient($type, $userIds, [
                'order_customer_id' => $entity->id, 'order_customer_no' => $entity->order_no
            ]);

            DB::commit();
            return response()->json([
                'errorCode' => HttpCode::EC_OK,
                'errorMessage' => '',
            ]);
        } catch (Exception $exception) {
            DB::rollBack();
            logError($exception . '- Data : ' . json_encode($request));
            return response()->json(
                [
                    'errorCode' => HttpCode::EC_APPLICATION_ERROR,
                    'errorMessage' => $exception->getMessage()
                ]
            );
        }
    }

    //Lưu thông tin xuất kho
    public function exportStore(Request $request)
    {
        try {
            $messages = [
                'location_destination_id.required' => 'Điểm nhận hàng là bắt buộc',
            ];
            $validation = Validator::make($request->all(), [
                'id' => 'required',
                'location_destination_id' => 'required',
                'ETD_date' => 'required',
                'ETD_time' => 'required',
            ], $messages);
            if ($validation->fails()) {
                return response()->json([
                    'errorCode' => HttpCode::EC_BAD_REQUEST,
                    'errorMessage' => $validation->messages()->all()
                ]);
            }

            DB::beginTransaction();
            $entity = $this->getRepository()->getItemById(Request::get('id'));
            if ($entity->status == config('constant.ORDER_CUSTOMER_STATUS.CHU_HANG_XAC_NHAN')) {
                $entity->status = config('constant.ORDER_CUSTOMER_STATUS.DA_XUAT_HANG');
            }
            $entity->status_goods = Request::get('status', 1);

            $location_destination_id = Request::get('location_destination_id', '');
            $ETD_date = Request::get('ETD_date', '');
            $ETD_time = Request::get('ETD_time', '');
            if (!empty($location_destination_id)) {
                $entity->location_destination_id = $location_destination_id;
            }
            if (!empty($ETD_date)) {
                $entity->ETD_date = $ETD_date;
            }
            if (!empty($ETD_time)) {
                $entity->ETD_time = $ETD_time;
            }

            $entity->save();

            $items = Request::get('data');
            $goodsTypeList = [];
            $goodsTypes = $this->getGoodsTypeRepos()->getItemsByIds(array_column($items, 'goods_type_id'));
            if ($goodsTypes)
                foreach ($goodsTypes as $goods) {
                    $goodsTypeList[$goods->id] = $goods;
                }

            $dataGoods = [];
            foreach ($items as $item) {
                OrderCustomerGoods::where('id', '=', $item['id'])->update([
                    'quantity_out' => $item['quantity_out']
                ]);

                if (isset($goodsTypeList[$item['goods_type_id']]) && $item['quantity_out_export'] > 0) {
                    $goodsType = $goodsTypeList[$item['goods_type_id']];
                    $dataGoods[] = [
                        'goods_type_id' => $goodsType->id,
                        'goods_unit_id' => $goodsType->goods_unit_id,
                        'insured_goods' => config('constant.yes'),
                        'quantity' => $item['quantity_out_export'],
                        'weight' => $goodsType->weight,
                        'volume' => $goodsType->volume,
                        'total_weight' => $goodsType->weight * $item['quantity_out_export'],
                        'total_volume' => $goodsType->volume * $item['quantity_out_export'],
                        'note' => null
                    ];
                }
            }

            $location_destination_id = Request::get('location_destination_id');
            $ETD_date = Request::get('ETD_date');
            $ETD_time = Request::get('ETD_time');
            if (!empty($location_destination_id) && $location_destination_id != 0)
                $entity->location_destination_id = $location_destination_id;
            if (!empty($ETD_date))
                $entity->ETD_date = AppConstant::convertDate($ETD_date, 'Y-m-d');
            if (!empty($ETD_time))
                $entity->ETD_time = AppConstant::convertTime($ETD_time, 'H:i');

            //Tạo dh vận tải
            $this->getOrderCustomerService()->createOrderFromOrderCustomer($entity, $dataGoods, config('constant.SOURCE_CREATE_C20_ORDER_CUSTOMER_FORM'));

            OrderCustomerHistory::insert([
                'order_customer_id' => $entity->id,
                'status' => $entity->status,
                'reason' => $entity->reason
            ]);

            //Bắn notify cho C20
            $userIds = $this->getAdminUserRepository()->getAllUserIsAdmin()->pluck('id')->toArray();
            $this->notificationService->notifyCustomerToC20(1, $userIds, ['order_customer_id' => $entity->id, 'order_customer_no' => $entity->order_no]);

            DB::commit();
            return response()->json([
                'errorCode' => HttpCode::EC_OK,
                'errorMessage' => '',
            ]);
        } catch (Exception $exception) {
            DB::rollBack();
            logError($exception . '- Data : ' . json_encode($request));
            return response()->json(
                [
                    'errorCode' => HttpCode::EC_APPLICATION_ERROR,
                    'errorMessage' => $exception->getMessage()
                ]
            );
        }
    }

    //Lấy danh sách đơn hàng của khách hàng
    public function event()
    {
        $start = Request::get('start');
        $end = Request::get('end');

        $userId = Auth::User()->id;
        $customer = $this->getCustomerRepos()->getCustomerTypeByUserId($userId);
        $events = $this->getRepository()->getEventByCustomerID($start, $end, $customer->id);
        return json_encode($events);
    }

    //Tính thời gian dự kiến
    public function calcETA(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'etd' => 'required',
                'location_destination_id' => 'required',
                'location_arrival_id' => 'required'
            ]);
            if ($validation->fails()) {
                return response()->json([
                    'errorCode' => HttpCode::EC_BAD_REQUEST,
                    'errorMessage' => $validation->messages()
                ]);
            }
            $etd = Request::get('etd');
            $location_destination_id = Request::get('location_destination_id');
            $location_arrival_id = Request::get('location_arrival_id');

            DB::beginTransaction();

            $result = $this->getOrderCustomerService()->calcETA($location_destination_id, $location_arrival_id, $etd);

            DB::commit();
            return response()->json([
                'errorCode' => HttpCode::EC_OK,
                'errorMessage' => '',
                'data' => [
                    'eta_date' => AppConstant::convertDate($result[0], 'd-m-Y'),
                    'eta_time' => AppConstant::convertTime($result[0], 'H:i'),
                    'distance' => $result[1]
                ]
            ]);
        } catch (Exception $exception) {
            DB::rollBack();
            logError($exception . '- Data : ' . json_encode($request));
            return response()->json(
                [
                    'errorCode' => HttpCode::EC_APPLICATION_ERROR,
                    'errorMessage' => $exception->getMessage()
                ]
            );
        }
    }

    //Tính giá dự kiến
    public function calcAmountEstimate(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'location_destination_id' => 'required',
                'location_arrival_id' => 'required',
                'weight' => 'required'
            ]);
            if ($validation->fails()) {
                return response()->json([
                    'errorCode' => HttpCode::EC_BAD_REQUEST,
                    'errorMessage' => $validation->messages()
                ]);
            }
            $weight = Request::get('weight');
            $location_destination_id = Request::get('location_destination_id');
            $location_arrival_id = Request::get('location_arrival_id');

            DB::beginTransaction();

            $result = $this->getOrderCustomerService()->calcAmountEstimate($location_destination_id, $location_arrival_id, $weight);

            DB::commit();
            return response()->json([
                'errorCode' => HttpCode::EC_OK,
                'errorMessage' => '',
                'data' => [
                    'amount_estimate' => $result[0],
                    'distance' => $result[1]
                ]
            ]);
        } catch (Exception $exception) {
            DB::rollBack();
            logError($exception . '- Data : ' . json_encode($request));
            return response()->json(
                [
                    'errorCode' => HttpCode::EC_APPLICATION_ERROR,
                    'errorMessage' => $exception->getMessage()
                ]
            );
        }
    }

    public function save(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'location_destination_id' => 'required',
                'location_arrival_id' => 'required',
                'ETD_date' => 'required',
            ]);
            if ($validation->fails()) {
                return response()->json([
                    'errorCode' => HttpCode::EC_BAD_REQUEST,
                    'errorMessage' => $validation->messages()
                ]);
            }
            $userId = Auth::User()->id;

            $entity = new OrderCustomer();
            if (!empty($request['id'])) {
                $entity = $this->getRepository()->getItemById($request['id']);
            } else {
                $systemCode = app('App\Http\Controllers\Backend\SystemCodeConfigController')->generateSystemCode(config('constant.sc_order_customer'), null, true);
                $entity->code = $entity->order_no = $entity->name = $systemCode;
                $currentDay = new DateTime();
                $entity->order_date = $currentDay->format('Y-m-d');
            }
            $entity->source_creation = config('constant.FROM_CLIENT');
            $entity->is_approved = 1;
            $customer = $this->getCustomerRepository()->getCustomerByUserId($userId);
            if ($customer) {
                $entity->customer_id = $customer->id;
                $entity->customer_name = $customer->full_name;
                $entity->customer_mobile_no = $customer->mobile_no;
            }
            $entity->ETD_date = empty($request['ETD_date']) ? null : AppConstant::convertDate($request['ETD_date'], 'Y-m-d');
            $entity->ETD_time = $request['ETD_time'];
            $entity->ETA_date = empty($request['ETA_date']) ? null : AppConstant::convertDate($request['ETA_date'], 'Y-m-d');
            $entity->ETA_time = $request['ETA_time'];

            $entity->distance = empty($request['distance']) ? 0 : $request['distance'];
            $entity->weight = empty($request['weight']) ? 0 : $request['weight'];
            $entity->volume = empty($request['volume']) ? 0 : $request['volume'];

            $entity->location_destination_id = empty($request['location_destination_id']) ? '' : $request['location_destination_id'];
            $entity->location_arrival_id = empty($request['location_arrival_id']) ? '' : $request['location_arrival_id'];

            DB::beginTransaction();

            $entity->save();

            if ($request['listVehicleGroup']) {
                $data = [];
                foreach ($request['listVehicleGroup'] as $item) {
                    $data[] = [
                        'order_customer_id' => $entity->id,
                        'vehicle_group_id' => empty($item['vehicle_group_id']) ? 0 : $item['vehicle_group_id'],
                        'vehicle_number' => empty($item['vehicle_number']) ? 1 : $item['vehicle_number']
                    ];
                }
                $entity->listVehicleGroups()->detach();
                $entity->listVehicleGroups()->sync($data);
            }

            DB::commit();
            return response()->json([
                'errorCode' => HttpCode::EC_OK,
                'errorMessage' => '',
            ]);
        } catch (Exception $exception) {
            logError($exception);
            DB::rollBack();
            return response()->json([
                'errorCode' => HttpCode::EC_APPLICATION_ERROR,
                'errorMessage' => HttpCode::getMessageForCode(HttpCode::EC_APPLICATION_ERROR)
            ]);
        }
    }
}
