<?php

namespace App\Repositories;

use App\Model\Entities\VehicleConfigSpecification;
use App\Repositories\Base\CustomRepository;

class VehicleConfigSpecificationRepository extends CustomRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    function model()
    {
        return VehicleConfigSpecification::class;
    }

    public function validator()
    {
        return \App\Validators\VehicleConfigSpecificationValidator::class;
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