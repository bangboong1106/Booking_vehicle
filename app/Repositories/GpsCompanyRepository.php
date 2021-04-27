<?php

namespace App\Repositories;

use App\Model\Entities\GpsCompany;
use App\Repositories\Base\CustomRepository;
use App\Validators\GpsCompanyValidator;

class GpsCompanyRepository extends CustomRepository
{

    /**
     * Specify Model class name
     *
     * @return string
     */
    function model()
    {
        return GpsCompany::class;
    }

    public function validator()
    {
        return GpsCompanyValidator::class;
    }
}