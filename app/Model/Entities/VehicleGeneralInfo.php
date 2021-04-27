<?php

namespace App\Model\Entities;

use App\Model\Base\ModelSoftDelete;

class VehicleGeneralInfo extends ModelSoftDelete
{
    protected $table = "vehicle_general_info";
    protected $fillable = ['vehicle_id', 'register_year', 'brand', 'weight_lifting_system', 'max_fuel','max_fuel_with_goods',
        'category_of_barrel', 'last_register_date', 'expire_register_date', 'last_insurance_date', 'expire_insurance_date'];
}