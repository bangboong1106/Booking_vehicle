<?php

namespace App\Model\Entities;

use App\Model\Base\ModelSoftDelete;
use Carbon\Carbon;


class ReportSchedule extends ModelSoftDelete
{
    protected $table = "report_schedules";
    protected $_alias = "report_schedule";

    protected $fillable = ['description', 'date_from', 'date_to', 'schedule_type', 'time_to_send', 'email', 'report_type'];

    public function getScheduleType()
    {
        return config('system.schedule_type.' . $this->schedule_type);
    }

    public function setDateFromAttribute($value)
    {
        $this->attributes['date_from'] = $value != null ? Carbon::createFromFormat('d-m-Y', $value)->format('Y-m-d') : null;
    }

    public function setDateToAttribute($value)
    {
        $this->attributes['date_to'] = $value != null ? Carbon::createFromFormat('d-m-Y', $value)->format('Y-m-d') : null;
    }
}