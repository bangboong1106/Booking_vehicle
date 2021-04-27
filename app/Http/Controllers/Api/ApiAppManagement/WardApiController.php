<?php

namespace App\Http\Controllers\Api\ApiAppManagement;

use App\Common\HttpCode;
use App\Http\Controllers\Base\ManagementApiController;
use App\Repositories\LocationRepository;
use App\Repositories\Management\WardManagementRepository;
use Illuminate\Http\Request;
use Exception;
use Validator;

class WardApiController extends ManagementApiController
{
    public function __construct(
        WardManagementRepository $customerRepository
    )
    {
        parent::__construct();
        $this->setRepository($customerRepository);
    }

}
