<?php

/**
 * Created by IntelliJ IDEA.
 * User: shine
 * Date: 10/6/18
 * Time: 12:22
 */

namespace App\Repositories;


use App\Model\Entities\TemplatePaymentMapping;
use App\Repositories\Base\CustomRepository;
use App\Validators\TemplatePaymentMappingValidator;

class TemplatePaymentMappingRepository extends CustomRepository
{
    function model()
    {
        return TemplatePaymentMapping::class;
    }

    public function validator()
    {
        return TemplatePaymentMappingValidator::class;
    }

    public function getTemplatePaymentMappingByTemplateMappingID($id)
    {
        return TemplatePaymentMapping::where('template_payment_id', $id)->get();
    }
}
