<?php

namespace App\Http\Controllers\Api\ApiAppClient\ApiWithoutAuthentication;

use App\Common\HttpCode;
use App\Http\Controllers\Base\ClientApiController;
use App\Repositories\Client\GoodsGroupClientRepository;
use App\Repositories\CustomerRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * Class GoodUnitController
 * @package App\Http\Controllers\Backend
 */
class GoodsGroupApiController extends ClientApiController
{
    public function __construct( CustomerRepository $customerRepository,GoodsGroupClientRepository $goodsGroupClientRepository)
    {
        parent::__construct($customerRepository);
        $this->setRepository($goodsGroupClientRepository);
    }

    public function list(Request $request)
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
