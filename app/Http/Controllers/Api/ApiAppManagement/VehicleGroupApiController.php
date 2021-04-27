<?php

namespace App\Http\Controllers\Api\ApiAppManagement;

use App\Common\HttpCode;
use App\Http\Controllers\Base\ManagementApiController;
use App\Repositories\Management\VehicleGroupManagementRepository;
use App\Repositories\VehicleGroupRepository;
use DB;
use Illuminate\Http\Request;
use Input;
use JWTAuth;
use Mockery\Exception;
use Validator;

class VehicleGroupApiController extends ManagementApiController
{

    public function __construct(VehicleGroupManagementRepository $VehicleGroupRepository)
    {
        parent::__construct();
        $this->setRepository($VehicleGroupRepository);

    }

    protected function getAll()
    {

        $data = $this->getRepository()->get();
        $results = [];
        foreach ($data as $entity) {
            $results[] = [
                'id' => $entity->id,
                'text' => $entity->name
            ];
        }

        $data = [
            'results' => $results
        ];

        $result = \Illuminate\Support\Facades\Response::json($data, 200);
        return $result;
    }

}