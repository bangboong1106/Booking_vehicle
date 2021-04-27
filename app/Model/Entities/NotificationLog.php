<?php

namespace App\Model\Entities;

use App\Model\Base\ModelSoftDelete;


class NotificationLog extends ModelSoftDelete
{
    protected $table = "notification_logs";
    protected $_alias = "notificationLog";

    protected $fillable = ['title', 'content', 'type', 'user_id', 'read_status', 'action_id', 'action_type', 'action_screen'];
    protected $_detailNameField = 'title';
}