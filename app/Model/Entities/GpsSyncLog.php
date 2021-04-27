<?php

namespace App\Model\Entities;

use App\Model\Base\ModelSoftDelete;

class GpsSyncLog extends ModelSoftDelete
{
    protected $table = "gps_sync_logs";
    protected $_alias = 'gps_sync_log';
    protected $fillable = ['request', 'response', 'error_code', 'error_message', 'type_request'];
}