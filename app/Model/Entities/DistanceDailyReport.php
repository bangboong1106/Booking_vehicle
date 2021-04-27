<?php

namespace App\Model\Entities;

use App\Model\Base\ModelSoftDelete;

class DistanceDailyReport extends ModelSoftDelete
{
    protected $table = "distance_daily_report";
    protected $fillable = ['route_id', 'vehicle_id', 'gps_id', 'reg_no', 'vehicle_plate', 'date', 'distance', 'distance_with_goods',
        'from_time', 'to_time', 'gps_company_id', 'status'];

}