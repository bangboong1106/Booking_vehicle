<?php

namespace App\Model\Entities;

use App\Model\Base\ModelSoftDelete;


class NotificationLogClient extends ModelSoftDelete
{
    protected $table = "notification_logs_client";

    protected $fillable = ['title', 'message', 'data', 'user_id', 'read_status', 'action_id', 'action_type', 'action_screen'];
}