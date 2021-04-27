<?php

namespace App\Model\Entities;

use App\Model\Base\ModelSoftDelete;


class FcmToken extends ModelSoftDelete
{
    protected $table = "fcm_tokens";
    protected $_alias = "fcmToken";

    protected $fillable = ['fcm_token', 'user_id', 'driver_id', 'expire_date', 'platform_type'];

}