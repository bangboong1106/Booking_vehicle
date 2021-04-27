<?php

namespace App\Repositories;

use App\Model\Entities\PayrollCustomerGroup;
use App\Repositories\Base\CustomRepository;
use App\Validators\PayrollCustomerGroupValidator;

class PayrollCustomerGroupRepository extends CustomRepository
{
    function model()
    {
        return PayrollCustomerGroup::class;
    }

    public function validator()
    {
        return PayrollCustomerGroupValidator::class;
    }

    public function getCustomerGroups($priceQuoteId)
    {
        if ($priceQuoteId == null)
            return null;
        $orders = $this->search([
            'payroll_id_eq' => $priceQuoteId
        ], ['customer_group_id as name_of_customer_group_id','customer_group_id'])->get();
        return $orders;
    }


}