<?php

/**
 * Created by IntelliJ IDEA.
 * User: shine
 * Date: 10/6/18
 * Time: 12:24
 */

namespace App\Model\Entities;

use App\Model\Base\ModelSoftDelete;

class TemplateExcelConverterMapping extends ModelSoftDelete
{
    protected $table = "template_excel_converter_mapping";

    protected $_alias = 'template_excel_converter_mapping';
    protected $fillable = [
        'template_excel_converter_id', 'field', 'column_index', 'formula',
         'ins_id', 'upd_id',
        'ins_date', 'upd_date', 'del_flag'
    ];
}
