<?php

namespace App\Model\Entities;

use App\Model\Base\ModelSoftDelete;

class VinhHienGPS extends ModelSoftDelete
{
    protected $table = "vinhhien_gps";
    protected $fillable = ['vehicle_plate', 'datetime', 'lat', 'lon',
        'address', 'current_total_km'];

}