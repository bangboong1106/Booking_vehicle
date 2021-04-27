<?php

namespace App\Model\Entities;

use App\Model\Base\ModelSoftDelete;

class VehicleConfigFile extends ModelSoftDelete
{
    protected $table = "vehicle_config_file";
    protected $_alias = 'vehicle_config_file';
    protected $fillable = ['active', 'file_name', 'is_required', 'is_show_expired', 'is_show_register'
        , 'allow_extension', 'note'];

    public function getOptionText($option)
    {
        return trans('common.' . $option);
    }

    public function getActive()
    {
        return config('system.active.' . $this->active);
    }

    public function getFileType()
    {
        return $this->allow_extension != null ? config('system.file_type.' . $this->allow_extension) : '';
    }
}