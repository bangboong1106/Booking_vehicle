<?php

namespace App\Http\Controllers\Api\ApiAppManagement;

use App\Common\HttpCode;
use App\Http\Controllers\Base\ManagementApiController;
use App\Repositories\Management\DistrictManagementRepository;
use App\Repositories\LocationRepository;
use Illuminate\Http\Request;
use Mockery\Exception;
use Validator;

class DistrictApiController extends ManagementApiController
{

    public function __construct(
        DistrictManagementRepository $customerRepository
    )
    {
        parent::__construct();
        $this->setRepository($customerRepository);
    }

}
