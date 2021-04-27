<?php

namespace App\Http\Controllers\Api\ApiAppManagement;

use App\Common\HttpCode;
use App\Http\Controllers\Base\ManagementApiController;
use App\Repositories\Management\ProvinceManagementRepository;
use Illuminate\Http\Request;
use Mockery\Exception;
use Validator;

class ProvinceApiController extends ManagementApiController
{
    public function __construct(
        ProvinceManagementRepository $customerRepository
    )
    {
        parent::__construct();
        $this->setRepository($customerRepository);
    }

}
