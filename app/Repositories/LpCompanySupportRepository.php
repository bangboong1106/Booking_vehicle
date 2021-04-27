<?php

namespace App\Repositories;

use App\Model\Entities\LpCompanySupport;
use App\Repositories\Base\CustomRepository;

class LpCompanySupportRepository extends CustomRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    function model()
    {
        return LpCompanySupport::class;
    }

    public function validator()
    {
        return \App\Validators\LpCompanySupportValidator::class;
    }
}