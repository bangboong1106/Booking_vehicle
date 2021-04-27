<?php

namespace App\Http\Controllers\Api\ApiAppManagement;

use App\Http\Controllers\Base\ManagementApiController;
use App\Repositories\Management\GoodsUnitManagementRepository;
use Mockery\Exception;
use Validator;

class GoodsUnitApiController extends ManagementApiController
{

    public function __construct(
        GoodsUnitManagementRepository $customerRepository
    )
    {
        parent::__construct();
        $this->setRepository($customerRepository);
    }

}
