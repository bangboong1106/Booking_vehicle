<?php

namespace App\Http\Controllers\Api;

use App\Common\HttpCode;
use App\Http\Controllers\Base\ApiController;
use App\Repositories\AppInfoRepository;
use Illuminate\Http\Request;
use Mockery\Exception;
use Validator;

class AppInfoApiController extends ApiController
{
    public function __construct(
        AppInfoRepository $appInfoRepository
    )
    {
        parent::__construct();
        $this->setRepository($appInfoRepository);
    }

    /**
     * Lấy thông tin phiên bản hiện tại của app theo id app
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     *
     */
    public function getAppInfoById(Request $request)
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
            } else {
                $data = $this->getRepository()->find($request["id"]);
                if (isset($data)) {
                    return response()->json([
                        'errorCode' => HttpCode::EC_OK,
                        'errorMessage' => '',
                        'data' => $data
                    ]);
                } else {
                    return response()->json(['errorCode' => HttpCode::EC_BAD_REQUEST,
                        'errorMessage' => HttpCode::getMessageForCode(HttpCode::EC_BAD_REQUEST)]);
                }
            }
        } catch (Exception $exception) {
            return response()->json(['errorCode' => HttpCode::EC_APPLICATION_ERROR,
                'errorMessage' => HttpCode::getMessageForCode(HttpCode::EC_APPLICATION_ERROR)]);
        }
    }
}
