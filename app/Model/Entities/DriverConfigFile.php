<?php

namespace App\Model\Entities;

use App\Model\Base\ModelSoftDelete;

class DriverConfigFile extends ModelSoftDelete
{
    protected $table = "driver_config_file";
    protected $_alias = "driver_config_file";
    protected $fillable = ['active', 'file_name', 'is_required', 'is_show_expired', 'is_show_register'
        , 'allow_extension', 'note'];

    protected $hidden = ['active', 'ins_id', 'upd_id', 'ins_date', 'upd_date', 'del_flag'];
    protected $_detailNameField = 'file_name';

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