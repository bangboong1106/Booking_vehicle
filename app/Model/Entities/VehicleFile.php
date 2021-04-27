<?php

namespace App\Model\Entities;

use App\Model\Base\ModelSoftDelete;

class VehicleFile extends ModelSoftDelete
{
    protected $table = "vehicle_file";
    protected $fillable = ['vehicle_id', 'vehicle_config_file_id', 'file_id', 'ref_no', 'note', 'expire_date', 'register_date'];

}