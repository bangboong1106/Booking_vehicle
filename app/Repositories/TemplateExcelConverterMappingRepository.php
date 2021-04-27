<?php

/**
 * Created by IntelliJ IDEA.
 * User: shine
 * Date: 10/6/18
 * Time: 12:22
 */

namespace App\Repositories;


use App\Model\Entities\TemplateExcelConverterMapping;
use App\Repositories\Base\CustomRepository;
use App\Validators\TemplateExcelConverterMappingValidator;

class TemplateExcelConverterMappingRepository extends CustomRepository
{
    function model()
    {
        return TemplateExcelConverterMapping::class;
    }

    public function validator()
    {
        return TemplateExcelConverterMappingValidator::class;
    }

    public function getItemsByTemplateExcelConverterID($id)
    {
        return TemplateExcelConverterMapping::where('template_excel_converter_id', $id)->get();
    }
}
