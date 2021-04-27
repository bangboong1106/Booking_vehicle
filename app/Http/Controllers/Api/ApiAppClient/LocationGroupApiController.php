<?php

namespace App\Http\Controllers\Api\ApiAppClient;

use App\Http\Controllers\Base\ClientApiController;
use App\Repositories\Client\LocationGroupClientRepository;
use App\Repositories\CustomerRepository;


/**
 * Class GoodUnitController
 * @package App\Http\Controllers\Backend
 */
class LocationGroupApiController extends ClientApiController
{
    public function __construct(CustomerRepository $customerRepository, LocationGroupClientRepository $repository)
    {
        parent::__construct($customerRepository);
        $this->setRepository($repository);
    }
}
