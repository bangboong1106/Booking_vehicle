<?php

namespace App\Repositories;

use App\Model\Entities\ContractType;
use App\Repositories\Base\CustomRepository;

class ContractTypeRepository extends CustomRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    function model()
    {
        return ContractType::class;
    }

    public function validator()
    {
        return \App\Validators\ContractTypeValidator::class;
    }

    public function getAll()
    {
        return $this->search()->pluck("name", "id");
    }
}