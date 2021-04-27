<?php
/**
 * Created by IntelliJ IDEA.
 * User: shine
 * Date: 10/6/18
 * Time: 12:22
 */

namespace App\Repositories;


use App\Model\Entities\TemplatePayment;
use App\Repositories\Base\CustomRepository;
use App\Validators\TemplatePaymentValidator;

class TemplatePaymentRepository extends CustomRepository
{
    function model()
    {
        return TemplatePayment::class;
    }

    public function validator()
    {
        return TemplatePaymentValidator::class;
    }

    public function getTemplatePayment()
    {
        return TemplatePayment::orderBy('id', 'desc')->first();
    }

    protected function _withRelations($query)
    {
        return $query->with(['templatePaymentMappings']);
    }
}