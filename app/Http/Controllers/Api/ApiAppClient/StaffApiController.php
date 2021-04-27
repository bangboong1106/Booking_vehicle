<?php

namespace App\Http\Controllers\Api\ApiAppClient;

use App\Http\Controllers\Base\ClientApiController;
use App\Repositories\Client\StaffClientRepository;
use Storage;
use Input;
use App\Repositories\CustomerRepository;

/**
 * Class AdminController
 * @package App\Http\Controllers\Backend
 */
class StaffApiController extends ClientApiController
{

    public function __construct(
        CustomerRepository $customerRepository,
        StaffClientRepository $StaffClientRepository
    ) {
        parent::__construct($customerRepository);
        $this->setRepository($StaffClientRepository);
    }
}
