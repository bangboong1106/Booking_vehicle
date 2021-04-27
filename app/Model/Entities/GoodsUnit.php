<?php

namespace App\Model\Entities;

use App\Model\Base\ModelSoftDelete;

class GoodsUnit extends ModelSoftDelete
{
    protected $table = "goods_unit";

    protected $_alias = 'goods_unit';
    protected $fillable = ['title', 'note', 'code', 'customer_id'];
    protected $_detailNameField = 'code';

    public function customer()
    {
        return $this->hasOne(Customer::class, 'id', 'customer_id');
    }
}