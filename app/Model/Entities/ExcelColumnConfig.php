<?php

namespace App\Model\Entities;

use App\Model\Base\ModelSoftDelete;


class ExcelColumnConfig extends ModelSoftDelete
{
    protected $table = "excel_column_config";
    protected $_alias = 'excel_column_config';

    protected $fillable = ['model', 'is_system', 'user_id', 'header_index', 'max_row'];

    public function excelColumnMappingConfigs()
    {
        return $this->hasMany(ExcelColumnMappingConfig::class, 'excel_column_config_id', 'id');
    }
}

