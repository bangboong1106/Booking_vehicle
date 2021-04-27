<?php

namespace App\Model\Entities;

use App\Model\Base\ModelSoftDelete;

class AppInfo extends ModelSoftDelete
{
    protected $table = "app_info";

    protected $_alias = 'app_info';
    protected $fillable = ['id', 'name', 'version_code', 'version_name', 'force_update'];
    protected $_detailNameField = 'code';
}
