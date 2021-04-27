<?php

namespace App\Http\Controllers\Api\ApiAppManagement;

use App\Common\HttpCode;
use App\Http\Controllers\Base\ManagementApiController;
use App\Repositories\Management\PaymentManagementRepository;
use Illuminate\Http\Request;
use Mockery\Exception;
use Validator;

class PaymentApiController extends ManagementApiController
{
    public function __construct(
        PaymentManagementRepository $customerRepository
    )
    {
        parent::__construct();
        $this->setRepository($customerRepository);
    }

}
