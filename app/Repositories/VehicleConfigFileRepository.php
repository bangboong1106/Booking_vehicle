<?php

namespace App\Repositories;

use App\Model\Entities\DriverConfigFile;
use App\Model\Entities\VehicleConfigFile;
use App\Repositories\Base\CustomRepository;

class VehicleConfigFileRepository extends CustomRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    function model()
    {
        return VehicleConfigFile::class;
    }

    public function validator()
    {
        return \App\Validators\VehicleConfigFileValidator::class;
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