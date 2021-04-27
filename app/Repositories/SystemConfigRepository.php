<?php

namespace App\Repositories;

use App\Model\Entities\SystemConfig;
use App\Repositories\Base\CustomRepository;

class SystemConfigRepository extends CustomRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    function model()
    {
        return SystemConfig::class;
    }

    public function validator()
    {
        return \App\Validators\SystemConfigValidator::class;
    }
}