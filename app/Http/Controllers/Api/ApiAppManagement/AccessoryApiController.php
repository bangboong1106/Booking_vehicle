<?php

namespace App\Http\Controllers\Api\ApiAppManagement;

use App\Http\Controllers\Base\ManagementApiController;
use App\Repositories\Management\AccessoryManagementRepository;
use Validator;

class AccessoryApiController extends ManagementApiController
{

    public function __construct(
        AccessoryManagementRepository $accessoryRepository
    )
    {
        parent::__construct();
        $this->setRepository($accessoryRepository);
    }

}
