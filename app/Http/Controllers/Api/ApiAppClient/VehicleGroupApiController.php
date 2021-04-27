<?php

namespace App\Http\Controllers\Api\ClientApiController;

use App\Http\Controllers\Base\ClientApiController;
use App\Repositories\DistrictRepository;
use App\Repositories\ProvinceRepository;
use App\Repositories\VehicleGroupRepository;
use App\Repositories\CustomerRepository;

use DB;
use Input;
use JWTAuth;
use Validator;

class VehicleGroupApiController extends ClientApiController
{

    public function __construct( CustomerRepository $customerRepository,VehicleGroupRepository $VehicleGroupRepository)
    {
        parent::__construct($customerRepository);

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