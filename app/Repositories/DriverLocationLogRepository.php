<?php

namespace App\Repositories;

use App\Model\Entities\DriverLocationLog;
use App\Repositories\Base\CustomRepository;

class DriverLocationLogRepository extends CustomRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    function model()
    {
        return DriverLocationLog::class;
    }

    public function validator()
    {
        return \App\Validators\DriverLocationLogValidator::class;
    }
}