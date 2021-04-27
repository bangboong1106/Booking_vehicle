<?php

namespace App\Http\Controllers\Api\ApiAppManagement;

use App\Http\Controllers\Base\ManagementApiController;
use App\Repositories\Management\LocationManagementRepository;
use Mockery\Exception;
use Validator;

class LocationApiController extends ManagementApiController
{

    public function __construct(
        LocationManagementRepository $customerRepository
    )
    {
        parent::__construct();
        $this->setRepository($customerRepository);
    }

}
