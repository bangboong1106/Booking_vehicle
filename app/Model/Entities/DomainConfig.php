<?php

namespace App\Model\Entities;

use App\Model\Base\ModelSoftDelete;


class DomainConfig extends ModelSoftDelete
{
    protected $table = 'domain_configs';
    protected $_alias = 'domain_config';

    protected $fillable = ['code', 'domain', 'description'];
}