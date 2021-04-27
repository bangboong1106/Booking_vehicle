<?php

namespace App\Model\Entities;

use App\Model\Base\ModelSoftDelete;


class DriverLocationLog extends ModelSoftDelete
{
    protected $table = "driver_location_logs";
    protected $_alias = "driverLocationLog";

    protected $fillable = ['d_uniqueID', 'request_data'];
}