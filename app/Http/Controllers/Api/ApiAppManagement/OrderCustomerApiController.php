<?php

namespace App\Http\Controllers\Api\ApiAppManagement;

use App\Common\HttpCode;
use App\Http\Controllers\Base\ManagementApiController;
use App\Repositories\Management\OrderCustomerManagementRepository;
use App\Repositories\OrderCustomerRepository;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Input;
use JWTAuth;
use Exception;
use Validator;

class OrderCustomerApiController extends ManagementApiController
{

    public function __construct(OrderCustomerManagementRepository $OrderCustomerRepository)
    {
        parent::__construct();
        $this->setRepository($OrderCustomerRepository);
    }

    // API lấy thông tin đơn hàng khách hàng trên BĐK ĐHKH
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
            $data = $this->getRepository()->getOrderCustomerControlBoard($fromDate, $toDate);
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
                    'exception'=> $exception
                ]);
        }
    }
}
