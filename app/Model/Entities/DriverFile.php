<?php

namespace App\Model\Entities;

use App\Model\Base\ModelSoftDelete;

class DriverFile extends ModelSoftDelete
{
    protected $table = "driver_file";
    protected $fillable = ['driver_id', 'driver_config_file_id', 'file_id', 'ref_no', 'note', 'expire_date', 'register_date'];

}