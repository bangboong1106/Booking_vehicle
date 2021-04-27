<?php

namespace App\Repositories;

use App\Model\Entities\VehicleSpecification;
use App\Repositories\Base\CustomRepository;

class VehicleSpecificationRepository extends CustomRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    function model()
    {
        return VehicleSpecification::class;
    }

    public function getVehicleSpecification($vehicle_id, $vehicle_config_specification_id)
    {
        if ($vehicle_id && $vehicle_config_specification_id)
            return $this->search([
                'vehicle_id_eq' => $vehicle_id,
                'vehicle_config_specification_id_eq' => $vehicle_config_specification_id,
            ])->first();
        return null;
    }

    public function getVehicleSpecificationWithVehicleID($vehicle_id)
    {
        if ($vehicle_id)
            return $this->search([
                'vehicle_id_eq' => $vehicle_id,
            ])->get();
        return null;
    }
}