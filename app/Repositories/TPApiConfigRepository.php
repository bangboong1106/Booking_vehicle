<?php

namespace App\Repositories;

use App\Model\Entities\TPApiConfig;
use App\Repositories\Base\CustomRepository;
use App\Validators\TPApiConfigValidator;

class TPApiConfigRepository extends CustomRepository
{
    function model()
    {
        return TPApiConfig::class;
    }

    public function validator()
    {
        return TPApiConfigValidator::class;
    }

    public function getApiConfig($partnerName)
    {
        return $this->search([
            'name_eq' => $partnerName
        ])->first();
    }
}