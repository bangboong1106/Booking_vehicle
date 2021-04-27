<?php

namespace App\Http\Controllers\Api;

use App\Common\HttpCode;
use App\Http\Controllers\Base\ApiController;
use App\Repositories\Driver\GoodsUnitDriverRepository;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Input;
use JWTAuth;
use Mockery\Exception;
use Validator;

class GoodsUnitApiController extends ApiController
{

    public function __construct(
        GoodsUnitDriverRepository $goodsUnitRepository
    ) {
        parent::__construct();
        $this->setRepository($goodsUnitRepository);
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
                $request["userId"] = Auth::User()->id;
                $items = $this->getRepository()->getDataList($request);
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
