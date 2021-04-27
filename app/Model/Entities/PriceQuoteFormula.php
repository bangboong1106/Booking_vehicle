<?php

namespace App\Model\Entities;

use App\Model\Base\ModelSoftDelete;

class PriceQuoteFormula extends ModelSoftDelete
{
    protected $table = "price_quote_formula";

    protected $_alias = 'price_quote_formula';
    protected $fillable = ['price_quote_id', 'location_group_destination_id', 'location_group_arrival_id', 'vehicle_group_id',
        'weight_from', 'weight_to', 'volume_from', 'volume_to', 'operator', 'price', 'goods_type_id'];


}