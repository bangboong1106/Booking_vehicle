<?php

namespace App\Http\Controllers\Api\ApiAppManagement;

use App\Common\HttpCode;
use App\Http\Controllers\Base\ManagementApiController;
use App\Repositories\Management\CustomerManagementRepository;
use App\Repositories\CustomerRepository;
use Illuminate\Http\Request;
use Exception;
use Validator;
use Illuminate\Support\Facades\Auth;

class CustomerApiController extends ManagementApiController
{

    public function __construct(
        CustomerManagementRepository $customerRepository
    )
    {
        parent::__construct();
        $this->setRepository($customerRepository);
    }

    // Lấy danh sách khách hàng thuộc quyền quản lý của user
    //Created by ptly 2020.06.22
    public function getCustomersByUser(Request $request)
    {
        try {
            $userId = Auth::user() != null ? Auth::user()->id : 1;
            $customers = $this->getRepository()->getDataListByUser($request, $userId);
            return response()->json([
                'errorCode' => HttpCode::EC_OK,
                'errorMessage' => '',
                'data' => $customers
            ]);

        } catch (Exception $exception) {
            logError($exception);
            return response()->json([
                'errorCode' => HttpCode::EC_APPLICATION_ERROR,
                'errorMessage' => HttpCode::getMessageForCode(HttpCode::EC_APPLICATION_ERROR)
            ]);
        }

    }

}
