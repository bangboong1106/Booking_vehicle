<?php

namespace App\Http\Controllers\Api\ApiAppManagement;

use App\Common\HttpCode;
use App\Http\Controllers\Base\ManagementApiController;
use App\Repositories\Management\QuotaManagementRepository;
use App\Repositories\QuotaRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Exception;
use Validator;

class QuotaApiController extends ManagementApiController
{

    public function __construct(
        QuotaManagementRepository $quotaRepository
    )
    {
        parent::__construct();
        $this->setRepository($quotaRepository);
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
            app('App\Http\Controllers\Backend\QuotaController')->_processCreateRelation($entity, $params['locations'], $params['costs']
                , isset($params['update_route']) ? $params['update_route'] : false);

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
                ]);
        }
    }

}
