<?php

namespace App\Model\Entities;

use App\Model\Base\ModelSoftDelete;
use Carbon\Carbon;


class AlertLog extends ModelSoftDelete
{
    protected $table = "alert_logs";
    protected $_alias = "alert_log";

    protected $fillable = ['name', 'title', 'content', 'alert_type', 'date_to_send', 'time_to_send'];
    protected $_detailNameField = 'name';

    public function getAlertType()
    {
        return config('system.alert_logs_type.' . $this->alert_type);
    }

    public function setDateToSendAttribute($value)
    {
        $this->attributes['date_to_send'] = $value != null ? Carbon::createFromFormat('d-m-Y', $value)->format('Y-m-d') : null;
    }
}