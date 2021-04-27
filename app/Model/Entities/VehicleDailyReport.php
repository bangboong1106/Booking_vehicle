<?php

namespace App\Model\Entities;

use App\Model\Base\ModelSoftDelete;

class VehicleDailyReport extends ModelSoftDelete
{
    protected $table = "vehicle_daily_report";
    protected $fillable = ['gps_id', 'reg_no', 'vehicle_plate', 'date', 'distance', 'door_open_count', 'over_speed_count',
        'max_speed', 'first_acc_on_time', 'last_acc_off_time', 'acc_time', 'run_time', 'idle_time', 'stop_time', 'sys_gps_time', 'date_gps_return'];

}