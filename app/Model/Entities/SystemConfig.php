<?php

namespace App\Model\Entities;

use App\Model\Base\ModelSoftDelete;


class SystemConfig extends ModelSoftDelete
{
    protected $table = 'system_config';
    protected $_alias = 'system_config';

    protected $fillable = ['key', 'value', 'description'];
}