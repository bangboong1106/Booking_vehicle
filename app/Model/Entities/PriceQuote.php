<?php

namespace App\Model\Entities;

use App\Model\Base\ModelSoftDelete;

class PriceQuote extends ModelSoftDelete
{
    protected $table = "price_quote";

    protected $_alias = 'price_quote';
    protected $fillable = ['code', 'name', 'description', 'date_from', 'date_to', 'type'
        , 'isApplyAll', 'isDefault', 'isDistance'];

    public function getType()
    {
        $typeList = config('system.price_quote_type');
        return array_key_exists($this->type, $typeList) ? $typeList[$this->type] : '';
    }

    public function getIsDefault()
    {
        $optionList = config('system.option');
        return array_key_exists($this->isDefault, $optionList) ? $optionList[$this->isDefault] : 'Không';
    }

    public function getIsApplyAll()
    {
        $optionList = config('system.option');
        return array_key_exists($this->isApplyAll, $optionList) ? $optionList[$this->isApplyAll] : 'Không';
    }

    public function getIsDistance()
    {
        $optionList = config('system.option');
        return array_key_exists($this->isDistance, $optionList) ? $optionList[$this->isDistance] : 'Không';
    }

    public function customerGroups()
    {
        return $this->belongsToMany('App\Model\Entities\CustomerGroup', 'price_quote_customer_group', 'price_quote_id', 'customer_group_id');
    }

    public function formulas()
    {
        return $this->hasMany('App\Model\Entities\PriceQuoteFormula', 'price_quote_id');
    }

    public function pointCharges()
    {
        return $this->hasMany('App\Model\Entities\PriceQuotePointCharge', 'price_quote_id');
    }


}