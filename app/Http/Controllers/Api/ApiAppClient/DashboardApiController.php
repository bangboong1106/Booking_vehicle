<?php

namespace App\Http\Controllers\Api\ApiAppClient;

use App\Common\HttpCode;
use App\Common\AppConstant;
use App\Http\Controllers\Base\ClientApiController;
use App\Repositories\Client\OrderCustomerClientRepository;
use App\Repositories\CustomerRepository;
use DB;
use Input;
use JWTAuth;
use Validator;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Auth;

class DashboardApiController extends ClientApiController
{
    public function __construct(
        CustomerRepository $customerRepository,
        OrderCustomerClientRepository $repository
    ) {
        parent::__construct($customerRepository);
        $this->setRepository($repository);
    }

    // Thống kê đơn hàng theo trạng thais
    public function status(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'type' => 'required',
            ]);
            if ($validation->fails()) {
                return response()->json([
                    'errorCode' => HttpCode::EC_BAD_REQUEST,
                    'errorMessage' => $validation->messages()
                ]);
            }
            $type = Request::get('type');
            $customerID = Request::get('id');
            if (empty($customerID)) {
                $userID = Auth::User()->id;
                $customer = $this->getCustomerRepository()->getCustomerByUserId($userID);
                $customerID = $customer->id;
            }
            $items = $this->getRepository()->getGroupStatusDataByType($type, $customerID, $request);

            return response()->json([
                'errorCode' => HttpCode::EC_OK,
                'errorMessage' => '',
                'data' => $items
            ]);
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

    // Thống kê đơn hàng theo số lượng
    public function order(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'type' => 'required',
            ]);
            if ($validation->fails()) {
                return response()->json([
                    'errorCode' => HttpCode::EC_BAD_REQUEST,
                    'errorMessage' => $validation->messages()
                ]);
            }
            $type = Request::get('type');
            $customerID = Request::get('id');
            $fromDate = Request::get('fromDate');
            $toDate = Request::get('toDate');
            if (empty($customerID)) {
                $userID = Auth::User()->id;
                $customer = $this->getCustomerRepository()->getCustomerByUserId($userID);
                $customerID = $customer->id;
            }
            $items = $this->getRepository()->getOrderDataByRangeTime($type, $customerID, $fromDate, $toDate);

            return response()->json([
                'errorCode' => HttpCode::EC_OK,
                'errorMessage' => '',
                'data' => $items
            ]);
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

    // Thống kê đơn hàng theo cước phí
    public function profit(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'type' => 'required',
            ]);
            if ($validation->fails()) {
                return response()->json([
                    'errorCode' => HttpCode::EC_BAD_REQUEST,
                    'errorMessage' => $validation->messages()
                ]);
            }
            $type = Request::get('type');
            $customerID = Request::get('id');
            $fromDate = Request::get('fromDate');
            $toDate = Request::get('toDate');

            $items = $this->getRepository()->getProfitDataByRangeTime($type, $customerID, $fromDate, $toDate);

            return response()->json([
                'errorCode' => HttpCode::EC_OK,
                'errorMessage' => '',
                'data' => $items
            ]);
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
