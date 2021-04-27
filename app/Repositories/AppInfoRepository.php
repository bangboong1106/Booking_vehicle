<?php

namespace App\Repositories;

use App\Model\Entities\AppInfo;
use App\Repositories\Base\CustomRepository;
use App\Validators\AppInfoValidator;
use App\Validators\GetStartedValidator;
use App\Validators\UserValidator;

class AppInfoRepository extends CustomRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    function model()
    {
        return AppInfo::class;
    }

    public function validator()
    {
        return GetStartedValidator::class;
    }

}
