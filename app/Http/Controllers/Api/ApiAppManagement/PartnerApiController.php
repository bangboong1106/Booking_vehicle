<?php

namespace App\Http\Controllers\Api\ApiAppManagement;

use App\Http\Controllers\Base\ManagementApiController;
use App\Repositories\Management\PartnerManagementRepository;
use Mockery\Exception;
use Validator;

class PartnerApiController extends ManagementApiController
{

    public function __construct(
        PartnerManagementRepository $customerRepository
    )
    {
        parent::__construct();
        $this->setRepository($customerRepository);
    }

}
