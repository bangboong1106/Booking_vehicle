<?php

namespace App\Http\Controllers\Api\ApiAppManagement;

use App\Common\HttpCode;
use App\Http\Controllers\Base\ManagementApiController;
use App\Repositories\Management\GoodsTypeManagementRepository;
use Illuminate\Http\Request;
use Mockery\Exception;
use Validator;

class GoodsApiController extends ManagementApiController
{
    public function __construct(
        GoodsTypeManagementRepository $customerRepository
    )
    {
        parent::__construct();
        $this->setRepository($customerRepository);
    }

}
