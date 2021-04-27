<?php

namespace App\Model\Entities;

use App\Model\Base\ModelSoftDelete;


class ExcelColumnMappingConfig extends ModelSoftDelete
{
    protected $table = "excel_column_mapping_config";
    protected $_alias = 'excel_column_mapping_config';

    protected $fillable = [
        'excel_column_config_id',
        'column_index',
        'column_name',
        'field',
        'original_field',
        'data',
        'default_value',
        'data_type',
        'function',
        'header_group',
        'comment',
        'collapse',
        'is_multiple',
        'entity',
        'mapping_data',
        'mapping_field',
        'is_key',
        'width',
        'is_import',
        'is_group',
        'nested_data_type',
        'nested_field' ,
        'nested_name',
        'nested_match',
        'background_color'
    ];
}
