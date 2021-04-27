<?php

namespace App\Model\Entities;

use App\Model\Base\ModelSoftDelete;


class SystemCodeConfig extends ModelSoftDelete
{
    protected $table = "system_code_config";
    protected $_alias = 'system_code_config';

    protected $fillable = ['prefix', 'suffix_length', 'type', 'end_suffix', 'code_tmp', 'suffix_tmp', 'is_generate_time', 'time_format'];
    protected static $_destroyRelations = [];

    public function getSystemCodeTypeText()
    {
        return config('system.system_code_type.' . $this->type);
    }
    public function getPreview()
    {
        return $this->prefix . sprintf('%0' . $this->suffix_length . 'd', 1);
    }
}
