<?php

namespace App\Http\Controllers\Api\ApiAppManager;

use App\Common\HttpCode;
use App\Http\Controllers\Base\ManagerApiController;
use App\Repositories\CustomerRepository;
use App\Repositories\DriverRepository;
use App\Repositories\FileRepository;
use App\Repositories\LocationRepository;
use App\Repositories\Management\OrderManagementRepository;
use App\Repositories\OrderCustomerRepository;
use App\Repositories\OrderFileRepository;
use App\Repositories\OrderHistoryRepository;
use App\Repositories\OrderRepository;
use App\Repositories\RouteCostRepository;
use App\Repositories\RoutesRepository;
use App\Repositories\TPActionSyncRepository;
use App\Repositories\VehicleRepository;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Input;
use JWTAuth;
use Exception;
use Validator;

class OrderApiController extends ManagerApiController
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
        TPActionSyncRepository $tpActionSyncRepository
    ) {
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
    }

    // API lưu thông tin
    // CreatedBy nlhoang 27/05/2020
    public function save(Request $request)
    {
        $params = $this->_getParams();

        try {
            DB::beginTransaction();
            $this->_setFormData($params);
            $entity = $this->_findEntityForStore();
            $entity->order_no = $entity->code;
            $entity->is_collected_documents = config("constant.no");
            $entity->is_insured_goods = config("constant.no");
            $entity->status_collected_documents = config("constant.CHUA_THU_DU");
            $entity->commission_type = config("constant.TONG_TIEN_HOA_HONG");
            $entity->commission_value = 0;
            $entity->locationDestinations[] = [
                'location_id' => $params['location_destination_id'],
                'date' => $params['ETD_date'],
                'time' => $params['ETD_time'],
            ];
            $entity->locationArrivals[] = [
                'location_id' => $params['location_arrival_id'],
                'date' => $params['ETA_date'],
                'time' => $params['ETA_time'],
            ];

            $params['locationDestinations'] = [
                [
                    'location_id' => $params['location_destination_id'],
                    'date' => $params['ETD_date'],
                    'time' => $params['ETD_time'],
                ]
            ];
            $params['locationArrivals'] = [
                [
                    'location_id' => $params['location_arrival_id'],
                    'date' => $params['ETA_date'],
                    'time' => $params['ETA_time'],
                ]
            ];

            $validator = $this->getRepository()->getValidator();
            $isValidate = isset($parameters['id']) ? $validator->validateUpdate($params) : $validator->validateCreate($params);
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

            $entity->save();
            app('App\Http\Controllers\Backend\OrderController')->_saveRelations($entity);
            $data = [
                'route_create' => 1,
                'vehicle_id' => isset($params['vehicle_id']) ? $params['vehicle_id'] : null,
                'primary_driver_id' => isset($params['primary_driver_id']) ? $params['primary_driver_id'] : null,
                'secondary_driver_id' => isset($params['secondary_driver_id']) ? $params['secondary_driver_id'] : null,
                'route_id' => isset($params['route_id']) ? $params['route_id'] : null,
            ];
            app('App\Http\Controllers\Backend\OrderController')->_processCreateRelation($entity, $data);

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
}
