<?php

namespace App\Http\Controllers\Api\ApiAppManagement;

use App\Common\HttpCode;
use App\Http\Controllers\Base\ManagementApiController;
use App\Repositories\Management\VehicleTeamManagementRepository;
use App\Repositories\VehicleTeamRepository;
use DB;
use Illuminate\Http\Request;
use Input;
use JWTAuth;
use Mockery\Exception;
use Validator;

class VehicleTeamApiController extends ManagementApiController
{

    public function __construct(VehicleTeamManagementRepository $VehicleTeamRepository)
    {
        parent::__construct();
        $this->setRepository($VehicleTeamRepository);

    }

}