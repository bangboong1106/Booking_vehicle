<?php

namespace App\Repositories\Client;

use App\Model\Entities\TypeShip;
use App\Repositories\TypeShipRepository;
use Exception;
use Illuminate\Support\Facades\DB;

class TypeShipClientRepository extends TypeShipRepository
{

    protected function getIgnoreClientID()
    {
        return true;
    }

}
