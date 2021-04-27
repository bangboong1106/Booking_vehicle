<?php

namespace App\Http\Controllers\Api\ApiAppManagement;

use App\Common\HttpCode;
use App\Http\Controllers\Base\ManagementApiController;
use App\Model\Entities\Routes;
use App\Repositories\Management\RoutesManagementRepository;
use App\Repositories\QuotaCostRepository;
use App\Repositories\RouteApprovalHistoryRepository;
use App\Repositories\RouteCostRepository;
use App\Repositories\OrderRepository;

use App\Repositories\RoutesRepository;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Input;
use JWTAuth;
use Exception;
use Validator;
use App\Model\Entities\ReceiptPayment;
use Illuminate\Support\Facades\Auth;


class RouteApiController extends ManagementApiController
{
    protected $_quotaCostRepository;
    protected $_orderRepository;
    protected $_routeApprovalHistoryRepository;
    protected $_routeCostRepository;

    /**
     * @return mixed
     */
    public function getQuotaCostRepository()
    {
        return $this->_quotaCostRepository;
    }

    /**
     * @param mixed $quotaCostRepository
     */
    public function setQuotaCostRepository($quotaCostRepository): void
    {
        $this->_quotaCostRepository = $quotaCostRepository;
    }

    /**
     * @return mixed
     */
    public function getRouteApprovalHistoryRepository()
    {
        return $this->_routeApprovalHistoryRepository;
    }


    /**
     * @param $routeApprovalHistoryRepository
     */
    public function setRouteApprovalHistoryRepository($routeApprovalHistoryRepository): void
    {
        $this->_routeApprovalHistoryRepository = $routeApprovalHistoryRepository;
    }

    /**
     * @return RouteCostRepository
     */
    public function getRouteCostRepository()
    {
        return $this->_routeCostRepository;
    }

    /**
     * @param $routeCostRepository
     */
    public function setRouteCostRepository($routeCostRepository): void
    {
        $this->_routeCostRepository = $routeCostRepository;
    }

    /**
     * @return RouteCostRepository
     */
    public function getOrderRepository()
    {
        return $this->_orderRepository;
    }

    /**
     * @param $orderRepository
     */
    public function setOrderRepository($orderRepository): void
    {
        $this->_orderRepository = $orderRepository;
    }


    public function __construct(
        RoutesManagementRepository $RouteRepository,
        QuotaCostRepository $quotaCostRepository,
        RouteApprovalHistoryRepository $routeApprovalHistoryRepository,
        RouteCostRepository $routeCostRepository,
        OrderRepository $orderRepository
    )
    {
        parent::__construct();
        $this->setRepository($RouteRepository);
        $this->setQuotaCostRepository($quotaCostRepository);
        $this->setRouteApprovalHistoryRepository($routeApprovalHistoryRepository);
        $this->setRouteCostRepository($routeCostRepository);
        $this->setOrderRepository($orderRepository);
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

            $entity->save();

            if (isset($params['quota_id'])) {
                $quotaCosts = $this->getQuotaCostRepository()->getCosts($params['quota_id']);
                if ($quotaCosts) {
                    $listCost = [];
                    foreach ($quotaCosts as $quotaCost) {
                        $cost['receipt_payment_id'] = $quotaCost->receipt_payment_id;
                        $cost['receipt_payment_name'] = $quotaCost->receipt_payment_name;
                        $cost['amount_admin'] = $quotaCost->amount;
                        $listCost[] = $cost;
                    }
                    $entity->listCost = $listCost;
                }
            }

            app('App\Http\Controllers\Backend\RouteController')->_processCreateRelation(
                $entity,
                'save',
                array_column($params['orders'], 'order_id'),
                $params['locations'],
                null
            );

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

    // API lấy lịch sử phê duyệt của chuyến
    // CreatedBy nlhoang 03/06/2020
    public function approvedHistory(Request $request)
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
            $data = $this->getRepository()->getApprovedHistory($id);
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

    /**
     * Api lấy danh sách chi phí chuyến xe
     * Created by ptly 2020.06.23
     */
    public function getRouteCost(Request $request)
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
            $data = $this->getRepository()->getRouteCost($id);
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

    /**
     * Phê duyệt chi phí
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function approve(Request $request)
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
            $listCost = $request->get('costs');
            $approvedNote = $request->get('note');
            $data = $this->_approve($id, $listCost, $approvedNote);
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


    /**
     * Phê duyệt chi phí
     * @param $id
     * @param $listCost
     * @return bool
     */
    public function _approve($id, $listCost, $approvedNote)
    {

        $total = 0;
        $entity = Routes::find($id);
        foreach ($listCost as $cost) {
            if (isset($cost['isInserted']) && $cost['isInserted'] == 'true') {
                //Cost dc thêm mới trên admin
                $routeCostItem = $this->getRouteCostRepository()->findFirstOrNew([]);
                $routeCostItem->route_id = $id;
                $routeCostItem->receipt_payment_id = $cost['receipt_payment_id'];
                $routeCostItem->receipt_payment_name = $cost['receipt_payment_name'];
                $routeCostItem->amount_driver = convertNumber($cost['amount_driver']);
                $routeCostItem->amount = convertNumber($cost['amount']);
                $total += $routeCostItem->amount;
                $routeCostItem->save();
            } else {
                $routeCostItem = $entity->costs()->where('id', '=', $cost['id'])->first();
                $routeCostItem->amount = convertNumber($cost['amount']);
                $total += $routeCostItem->amount;
                $routeCostItem->save();
            }
        }

        $entity->final_cost = $total;
        $entity->is_approved = 1;
        $entity->approved_id = Auth::User()->id;
        $entity->approved_date = now();
        $entity->approved_note = $approvedNote;
        $entity->save();

        //Lưu history
        $routeApprovalHistory = $this->getRouteApprovalHistoryRepository()->findFirstOrNew([]);
        $routeApprovalHistory->route_id = $entity->id;
        $routeApprovalHistory->approved_id = Auth::User()->id;
        $routeApprovalHistory->approved_date = now();
        $routeApprovalHistory->approved_note = $approvedNote;
        $routeApprovalHistory->save();

        $totalCostAdmin = 0;
        $totalCostDriver = 0;
        foreach ($entity->costs as &$cost) {
            $cost->receipt_payment_name = $cost->receiptPayment ? $cost->receiptPayment->name : $cost->receipt_payment_name;
            $totalCostAdmin += $cost->amount_admin ? $cost->amount_admin : 0;
            $totalCostDriver += $cost->amount_driver ? $cost->amount_driver : 0;
        }
        $entity->total_cost_admin = $totalCostAdmin;
        $entity->total_cost_driver = $totalCostDriver;
        $entity->listCost = $entity->costs->toArray();
    }


    //API lấy thông tin đơn hàng khi thêm đơn hàng từ combobox đơn hàng
    function location(Request $request)
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
            }

            $orderId = Request::get('order_id', null);
            $order = $this->getOrderRepository()->getOrdersByIds([$orderId])->first();

            $location = [];
            if ($order != null) {
                $location = [
                    'destination_location_id' => $order->location_destination_id,
                    'name_of_destination_location_id' => $order->location_destination_title,
                    'destination_location_date' => $order->ETD_date,
                    'destination_location_time' => $order->ETD_time,
                    'arrival_location_id' => $order->location_arrival_id,
                    'name_of_arrival_location_id' => $order->location_arrival_title,
                    'arrival_location_date' => $order->ETA_date,
                    'arrival_location_time' => $order->ETA_time,
                    'order_id' => $order->id,
                    'order_code' => $order->order_code,
                    'order_cost' => $order->extend_cost ? $order->extend_cost : 0
                ];
            }

            $vehicleAndDriver = $this->getOrderRepository()->getVehicleAndDriverForOrder($orderId);
            $data = [
                'location' => $location,
                'vehicleAndDriver' => $vehicleAndDriver
            ];


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

    // API lấy thông tin chuyến xe trên Lệnh vận chuyển
    // CreatedBy ptly 2020.08.25
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
            $data = $this->getRepository()->getRouteControlBoard($fromDate, $toDate);
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
                    'errorMessage' => HttpCode::getMessageForCode(HttpCode::EC_APPLICATION_ERROR),
                    'exception' => $exception
                ]);
        }
    }

}
