<?php

namespace App\Model\Entities;

use App\Model\Base\ModelSoftDelete;

class VehicleConfigSpecification extends ModelSoftDelete
{
    protected $table = "vehicle_config_specification";
    protected $_alias = 'vehicle_config_specification';
    protected $fillable = ['active', 'name', 'type', 'group_unit', 'tab_view', 'is_required'];

    public function getActive()
    {
        return config('system.active.' . $this->active);
    }

    public function getType()
    {
        return config('system.column_type.' . $this->type);
    }
}