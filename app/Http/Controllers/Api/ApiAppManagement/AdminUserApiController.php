<?php

namespace App\Http\Controllers\Api\ApiAppManagement;


use App\Http\Controllers\Base\ManagementApiController;
use App\Repositories\Management\AdminUserManagementRepository;
use DB;
use Input;
use JWTAuth;
use Validator;

class AdminUserApiController extends ManagementApiController
{

    public function __construct(AdminUserManagementRepository $adminUserManagementRepository)
    {
        parent::__construct();
        $this->setRepository($adminUserManagementRepository);
    }
}
