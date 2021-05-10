<?php

namespace App\Repositories;

use App\Model\Entities\TypeShip;

use App\Repositories\Base\CustomRepository;
use App\Validators\TypeShipValidator;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class TypeShipRepository extends CustomRepository
{
    /**
     * @return string
     */
    function model()
    {
        return TypeShip::class;
    }

    /**
     * @return null|string
     */
    public function validator()
    {
        return TypeShipValidator::class;
    }

    public function getListTypeShip()
    {
        return TypeShip::all();
    }

    /**
     * @param $q
     * @return array
     */

}
