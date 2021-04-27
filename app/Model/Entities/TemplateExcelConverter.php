<?php

/**
 * Created by IntelliJ IDEA.
 * User: shine
 * Date: 10/6/18
 * Time: 12:24
 */

namespace App\Model\Entities;

use App\Model\Base\ModelSoftDelete;

class TemplateExcelConverter extends ModelSoftDelete
{
    protected $table = "template_excel_converter";

    protected $_alias = 'template_excel_converter';
    protected $fillable = [
        'title', 'file_id', 'description', 'model', 'header_row_index', 'max_row', 'is_use_convert_sheet'
    ];

    public function templateExcelConverterMappings()
    {
        return $this->hasMany(TemplateExcelConverterMapping::class, 'template_excel_converter_id', 'id');
    }

    public function getFile()
    {
        return $this->hasOne(File::class, 'file_id', 'file_id');
    }
}
