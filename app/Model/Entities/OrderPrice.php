<?php

namespace App\Model\Entities;

use App\Model\Base\ModelSoftDelete;

class OrderPrice extends ModelSoftDelete
{
    protected $table = "order_price";

    protected $_alias = 'order_price';
    protected $fillable = [
        'order_id', 'price_quote_id', 'amount', 'description', 'is_approved', 'approved_id', 'approved_note', 'approved_date'
    ];



    public function order()
    {
        return $this->hasOne(Order::class, 'id', 'order_id');
    }

    public function priceQuote()
    {
        return $this->hasOne(PriceQuote::class, 'id', 'price_quote_id');
    }
}
