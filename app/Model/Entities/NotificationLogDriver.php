<?php

namespace App\Model\Entities;

use App\Model\Base\ModelSoftDelete;


class NotificationLogDriver extends ModelSoftDelete
{
    protected $table = "notification_logs_driver";

    protected $fillable = ['title', 'message', 'data', 'driver_id', 'read_status', 'action_id', 'action_type', 'action_screen'];
}