<?php

namespace App\Repositories;

use App\Model\Entities\DriverVehicle;
use App\Repositories\Base\CustomRepository;

class DriverVehicleRepository extends CustomRepository
{
    function model()
    {
        return DriverVehicle::class;
    }

    public function getItemsByDriverID($driverId)
    {
        if ($driverId) {
            return $this->search([
                'driver_id_eq' => $driverId
            ])->get();
        }
        return null;
    }

    public function getItemsByVehicleID($vehicleId)
    {
        if ($vehicleId) {
            return $this->search([
                'vehicle_id_eq' => $vehicleId,
            ])->get();
        }
        return null;
    }

    public function getItemByVehicleID($vehicleId)
    {
        if ($vehicleId) {
            return $this->search([
                'vehicle_id_eq' => $vehicleId,
            ])->first();
        }
        return null;
    }
}
