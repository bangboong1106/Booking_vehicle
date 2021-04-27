<?php

namespace App\Model\Entities;

use App\Model\Base\ModelSoftDelete;

class PriceQuotePointCharge extends ModelSoftDelete
{
    protected $table = "price_quote_point_charge";

    protected $_alias = 'price_quote_point_charge';
    protected $fillable = ['price_quote_id', 'operator', 'vehicle_group_id', 'weight_from', 'weight_to', 'volume_from', 'volume_to', 'price',
        'goods_type_id'];


}