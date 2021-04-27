<?php

namespace App\Repositories;

use App\Model\Entities\DomainConfig;
use App\Repositories\Base\CustomRepository;

class DomainConfigRepository extends CustomRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    function model()
    {
        return DomainConfig::class;
    }

    public function validator()
    {
        return \App\Validators\DomainConfigValidator::class;
    }

    public function getDomainConfig($code)
    {
        return $this->search([
            'code_eq' => $code,
            'del_flag_eq' => '0'
        ])->first();
    }
}