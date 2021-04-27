<?php

namespace App\Http\Controllers\Base;

use App\Common\HttpCode;
use App\Helpers\Url;
use Illuminate\Http\Request;
use Mockery\Exception;
use DB;
use Input;
use JWTAuth;
use Validator;

/**
 * Class ApiController
 * @package App\Http\Controllers\Base
 */
class ApiController extends BaseController
{
    protected $_area = 'api';

    /**
     * ApiController constructor.
     */
    public function __construct()
    {
        try {
            Url::setCurrentControllerName($this->getCurrentControllerName());
            $this->setEventPrefix(getConstant('EVENT_CONTROLLER_TYPE', 'controller'));
            $this->setEventSuffix($this->getArea() . '.' . $this->getCurrentControllerName());
        } catch (\Exception $e) {
            logError($e);
        }
    }

    protected function _buildResponse($data = [])
    {
        return array_merge(array(
            'ok' => $this->isOk(),
            'message' => $this->getMessage(),
            'data' => $this->getData(),
            'meta' => $this->getMetaData()
        ), $data);
    }

    public function getCurrentUser()
    {
        return apiGuard()->user();
    }

}
