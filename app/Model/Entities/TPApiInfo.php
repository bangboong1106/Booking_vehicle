<?php

namespace App\Model\Entities;

use App\Model\Base\ModelSoftDelete;

class TPApiInfo extends ModelSoftDelete
{
    protected $table = "3p_api_info";

    protected $_alias = '3p_api_info';
    protected $fillable = ['partner_name', 'action', 'url', 'note'];

}