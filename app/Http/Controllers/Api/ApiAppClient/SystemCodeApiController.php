<?php

namespace App\Http\Controllers\Api\ApiAppClient;

use App\Common\HttpCode;
use App\Http\Controllers\Base\ClientApiController;
use Illuminate\Http\Request;
use Input;
use Exception;
use Validator;

class SystemCodeApiController extends ClientApiController
{

    // Hàm sinh tự động mã
    // CreatedBy nlhoang 28/05/2020
    public function generate(Request $request)
    {
        try {
            $model = $request->get('model');
            $systemCode = config('constant.sc_' . $model);
            $code = app('App\Http\Controllers\Backend\SystemCodeConfigController')->generateSystemCode($systemCode, null, false);
            return response()->json([
                'errorCode' => HttpCode::EC_OK,
                'errorMessage' => '',
                'data' => $code
            ]);
        } catch (Exception $exception) {
            logError($exception);
            return response()->json(
                [
                    'errorCode' => HttpCode::EC_APPLICATION_ERROR,
                    'errorMessage' => HttpCode::getMessageForCode(HttpCode::EC_APPLICATION_ERROR)
                ]);
        }

    }
}
