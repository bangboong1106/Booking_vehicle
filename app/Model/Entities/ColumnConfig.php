<?php

namespace App\Model\Entities;

use App\Model\Base\ModelSoftDelete;


class ColumnConfig extends ModelSoftDelete
{
    protected $table = "column_config";
    protected $_alias = 'column_config';

    protected $fillable = ['user_id', 'table_id', 'config','sort_field', 'sort_type', 'page_size'];

}