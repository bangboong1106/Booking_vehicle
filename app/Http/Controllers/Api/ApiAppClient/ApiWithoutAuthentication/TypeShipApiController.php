<?php

namespace App\Http\Controllers\Api\ApiAppClient\ApiWithoutAuthentication;

use App\Common\HttpCode;
use App\Http\Controllers\Base\ClientApiController;
use App\Repositories\Client\TypeShipClientRepository;
use App\Repositories\CustomerRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TypeShipApiController extends ClientApiController
{
    public function __construct( CustomerRepository $customerRepository,TypeShipClientRepository $typeShipClientRepository)
    {
        parent::__construct($customerRepository);
        $this->setRepository($typeShipClientRepository);
    }

    public function list(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'pageSize' => 'required',
                'pageIndex' => 'required',
            ]);
            if ($validation->fails()) {
                return response()->json([
                    'errorCode' => HttpCode::EC_BAD_REQUEST,
                    'errorMessage' => $validation->messages()
                ]);
            } else {
                $items = $this->_repository->getDataList(null, null, $request);
                return response()->json([
                    'errorCode' => HttpCode::EC_OK,
                    'errorMessage' => '',
                    'data' => $items
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

}
