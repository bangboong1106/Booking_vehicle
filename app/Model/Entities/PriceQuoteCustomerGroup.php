<?php

namespace App\Model\Entities;

use App\Model\Base\ModelSoftDelete;

class PriceQuoteCustomerGroup extends ModelSoftDelete
{
    protected $table = "price_quote_customer_group";

    protected $_alias = 'price_quote_customer_group';
    protected $fillable = ['price_quote_id', 'customer_group_id'];

    public function customerGroup()
    {
        return $this->hasOne(CustomerGroup::class, 'id', 'customer_group_id');
    }
}