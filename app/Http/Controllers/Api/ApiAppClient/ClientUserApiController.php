<?php

namespace App\Http\Controllers\Api\ApiAppClient;

use App\Http\Controllers\Base\ClientApiController;
use App\Repositories\Client\ClientUserClientRepository;
use Storage;
use Input;
use App\Repositories\CustomerRepository;

/**
 * Class AdminController
 * @package App\Http\Controllers\Backend
 */
class ClientUserApiController extends ClientApiController
{

    public function __construct(
        CustomerRepository $customerRepository,
        ClientUserClientRepository $clientUserClientRepository
    ) {
        parent::__construct($customerRepository);
        $this->setRepository($clientUserClientRepository);
    }
}
