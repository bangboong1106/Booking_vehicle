<?php

namespace App\Model\Entities;

use App\Model\Base\ModelSoftDelete;

class VehicleSpecification extends ModelSoftDelete
{
    protected $table = "vehicle_specification";
    protected $fillable = ['vehicle_id', 'vehicle_config_specification_id', 'value', 'unit'];

}