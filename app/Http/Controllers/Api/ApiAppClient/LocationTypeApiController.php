<?php

namespace App\Http\Controllers\Api\ApiAppClient;

use App\Http\Controllers\Base\ClientApiController;
use App\Repositories\Client\GoodsUnitClientRepository;
use App\Repositories\Client\LocationTypeClientRepository;
use App\Repositories\GoodsUnitRepository;
use App\Repositories\CustomerRepository;


/**
 * Class GoodUnitController
 * @package App\Http\Controllers\Backend
 */
class LocationTypeApiController extends ClientApiController
{
    public function __construct(CustomerRepository $customerRepository, LocationTypeClientRepository $repository)
    {
        parent::__construct($customerRepository);
        $this->setRepository($repository);
    }
}
