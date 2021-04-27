<?php

namespace App\Model\Entities;

use App\Model\Base\ModelSoftDelete;

class OrderGood extends ModelSoftDelete
{
    protected $table = "order_goods";

    protected $_alias = 'order_good';
    protected $fillable = ['order_id', 'good_types_id', 'quantity', 'goods_unit_id', 'insured_goods', 'note', 'weight'
        , 'volume', 'total_weight', 'total_volume', 'goods_type_id'];


}