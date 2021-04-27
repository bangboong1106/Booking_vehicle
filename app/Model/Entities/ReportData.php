<?php

namespace App\Model\Entities;

use App\Model\Base\ModelSoftDelete;

class ReportData extends ModelSoftDelete
{
    protected $table = "report_data";
    protected $_alias = 'report_data';
    protected $fillable = ['report_id', 'report_name', 'vehicle', 'client', 'driver', 'vehicle_team',
        'status_all', 'status_complete', 'status_incomplete', 'status_on_time', 'status_late', 'status_future',
        'date', 'month'];
}