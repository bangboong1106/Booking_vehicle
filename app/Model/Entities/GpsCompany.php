<?php

namespace App\Model\Entities;

use App\Model\Base\ModelSoftDelete;

class GpsCompany extends ModelSoftDelete
{
    protected $table = "gps_company";

    protected $_alias = 'gps_company';
    protected $fillable = ['name', 'web_service_wsdl', 'user', 'key', 'function_name', 'vehicle_function_name'];
    protected $_detailNameField = 'name';
}