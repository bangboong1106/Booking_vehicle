<?php

namespace App\Model\Entities;

use App\Model\Base\ModelSoftDelete;

class TPApiConfig extends ModelSoftDelete
{
    protected $table = "3p_api_configs";

    protected $_alias = '3p_api_configs';
    protected $fillable = ['name', 'client_id', 'client_secret', 'grant_type', 'username', 'password',
        'env', 'access_token', 'token_type', 'refresh_token', 'expires_in', 'scope', 'expired', 'request_header_authen', 'note'
    ];
}