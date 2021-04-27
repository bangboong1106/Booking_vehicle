<?php
/**
 * Created by IntelliJ IDEA.
 * User: shine
 * Date: 10/6/18
 * Time: 12:22
 */

namespace App\Repositories;

use App\Model\Entities\TemplateExcelConverter;
use App\Repositories\Base\CustomRepository;
use App\Validators\TemplatePaymentValidator;

class TemplateExcelConverterRepository extends CustomRepository
{
    function model()
    {
        return TemplateExcelConverter::class;
    }

    public function validator()
    {
        return TemplatePaymentValidator::class;
    }

    protected function _withRelations($query)
    {
        return $query->with(['templateExcelConverterMappings']);
    }
}