<?php

namespace App\Http\Controllers\Api\ApiAppClient;

use App\Http\Controllers\Base\ClientApiController;
use App\Repositories\Client\CustomerDefaultDataClientRepository;
use App\Repositories\CustomerRepository;
use Input;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use JWTAuth;
use Exception;
use Validator;
use App\Common\HttpCode;

/**
 * Class GoodUnitController
 * @package App\Http\Controllers\Backend
 */
class CustomerDefaultDataApiController extends ClientApiController
{
    public function __construct(CustomerRepository $customerRepository, CustomerDefaultDataClientRepository $repository)
    {
        parent::__construct($customerRepository);
        $this->setRepository($repository);
    }

    // Lấy dữ liệu mặc định của khách hàng
    // CreatedBy nlhoang 20/11/2020
    protected function defaultData(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'client_id' => 'required'
            ]);
            if ($validation->fails()) {
                return response()->json([
                    'errorCode' => HttpCode::EC_BAD_REQUEST,
                    'errorMessage' => $validation->messages()
                ]);
            }
            $client_id = Request::get('client_id', 0);

            $defaultData = $this->getRepository()->getDefaultDataByClientID($client_id);

            return response()->json([
                'errorCode' => HttpCode::EC_OK,
                'errorMessage' => '',
                'data' => $defaultData
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
