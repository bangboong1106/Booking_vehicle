<?php

namespace App\Repositories;

use App\Model\Entities\DriverConfigFile;
use App\Repositories\Base\CustomRepository;

class DriverConfigFileRepository extends CustomRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    function model()
    {
        return DriverConfigFile::class;
    }

    public function validator()
    {
        return \App\Validators\DriverConfigFileValidator::class;
    }

    public function getAll()
    {
        return $this->search([
            'active_eq' => 1,
            'sort_type' => 'asc',
            'sort_field' => 'id'
        ])->get();
    }
}