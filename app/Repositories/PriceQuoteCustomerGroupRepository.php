<?php

namespace App\Repositories;

use App\Model\Entities\PriceQuoteCustomerGroup;
use App\Repositories\Base\CustomRepository;
use App\Validators\PriceQuoteCustomerGroupValidator;

class PriceQuoteCustomerGroupRepository extends CustomRepository
{
    function model()
    {
        return PriceQuoteCustomerGroup::class;
    }

    public function validator()
    {
        return PriceQuoteCustomerGroupValidator::class;
    }

    public function getCustomerGroups($priceQuoteId)
    {
        if ($priceQuoteId == null)
            return null;
        $orders = $this->search([
            'price_quote_id_eq' => $priceQuoteId
        ], ['customer_group_id as name_of_customer_group_id','customer_group_id'])->get();
        return $orders;
    }


}