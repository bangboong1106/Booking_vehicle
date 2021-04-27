<?php

namespace App\Repositories;

use App\Model\Entities\VehicleGeneralInfo;
use App\Repositories\Base\CustomRepository;

class VehicleGeneralInfoRepository extends CustomRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    function model()
    {
        return VehicleGeneralInfo::class;
    }

    public function getVehicleGeneralByVehicleId($vehicleId)
    {
        if (!$vehicleId)
            return null;
        return $this->search([
            'vehicle_id_eq' => $vehicleId
        ])->first();
    }
}