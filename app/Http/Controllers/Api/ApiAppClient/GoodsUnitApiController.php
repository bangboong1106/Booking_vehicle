<?php

namespace App\Http\Controllers\Api\ApiAppClient;

use App\Http\Controllers\Base\ClientApiController;
use App\Repositories\Client\GoodsUnitClientRepository;
use App\Repositories\GoodsUnitRepository;
use App\Repositories\CustomerRepository;


/**
 * Class GoodUnitController
 * @package App\Http\Controllers\Backend
 */
class GoodsUnitApiController extends ClientApiController
{
    public function __construct( CustomerRepository $customerRepository,GoodsUnitClientRepository $GoodsUnitRepository)
    {
        parent::__construct($customerRepository);
        $this->setRepository($GoodsUnitRepository);
    }

    public function goodUnits()
    {
        $entities = $this->getRepository() -> getData() -> toArray();

        $results = [];
        foreach ($entities as $entity) {
            $results[] = [
                'id' => $entity['id'],
                'text' => $entity['title'],
            ];
        }

        $data = [
            'results' => $results
        ];

        $result = \Illuminate\Support\Facades\Response::json($data, 200);
        return $result;
    }
}
