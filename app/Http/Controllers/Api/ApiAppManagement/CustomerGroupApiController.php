<?php

namespace App\Http\Controllers\Api\ApiAppManagement;


use App\Http\Controllers\Base\ManagementApiController;
use App\Repositories\Management\CustomerGroupManagementRepository;
use DB;
use Input;
use JWTAuth;
use Validator;

class CustomerGroupApiController extends ManagementApiController
{

    public function __construct(CustomerGroupManagementRepository $CustomerGroupRepository)
    {
        parent::__construct();
        $this->setRepository($CustomerGroupRepository);
    }
}
