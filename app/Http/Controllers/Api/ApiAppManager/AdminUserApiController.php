<?php

namespace App\Http\Controllers\Api\ApiAppManager;


use App\Http\Controllers\Base\ManagerApiController;
use App\Repositories\Management\AdminUserManagementRepository;
use DB;
use Input;
use JWTAuth;
use Validator;

class AdminUserApiController extends ManagerApiController
{

    public function __construct(AdminUserManagementRepository $adminUserManagementRepository)
    {
        parent::__construct();
        $this->setRepository($adminUserManagementRepository);
    }
}
