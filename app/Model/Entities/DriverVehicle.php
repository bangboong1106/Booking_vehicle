<?php

namespace App\Model\Entities;

use App\Model\Base\ModelSoftDelete;

class DriverVehicle extends ModelSoftDelete
{
    protected $table = "driver_vehicle";

    protected $_alias = 'driver_vehicle';
    protected $fillable = ['driver_id', 'vehicle_id', 'driver_type', 'active', 'note'];
}