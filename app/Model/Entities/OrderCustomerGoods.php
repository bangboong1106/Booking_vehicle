<?php

namespace App\Model\Entities;

use App\Model\Base\ModelSoftDelete;

class OrderCustomerGoods extends ModelSoftDelete
{
    protected $table = "order_customer_goods";
    protected $fillable = ['order_customer_id', 'goods_type_id', 'goods_unit_id', 'insured_goods', 'quantity', 'weight', 'volume', 'total_weight', 'total_volume',
        'quantity_out'];

    public function file()
    {
        return $this->hasOne(File::class, 'id', 'file_id');
    }
}
