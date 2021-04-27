<?php

namespace App\Model\Entities;

use App\Model\Base\ModelSoftDelete;

class GoodsType extends ModelSoftDelete
{
    protected $table = "goods_type";

    protected $_alias = 'goods_type';
    protected $fillable = ['title', 'volume', 'weight', 'goods_unit_id', 'note', 'code', 'goods_group_id','file_id', 'customer_id', 'amount', 'in_amount', 'out_amount'];
    protected $_detailNameField = 'code';

    public function goodsUnit()
    {
        return $this->hasOne(GoodsUnit::class, 'id', 'goods_unit_id');
    }

    public function goodsGroup()
    {
        return $this->hasOne(GoodsGroup::class, 'id', 'goods_group_id');
    }

    public function getFile()
    {
        return $this->hasOne(File::class, 'file_id', 'file_id');
    }

    public function customer()
    {
        return $this->hasOne(Customer::class, 'id', 'customer_id');
    }
}
