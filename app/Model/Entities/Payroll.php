<?php

namespace App\Model\Entities;

use App\Model\Base\ModelSoftDelete;

class Payroll extends ModelSoftDelete
{
    protected $table = "payroll";

    protected $_alias = 'payroll';
    protected $fillable = ['code', 'name', 'description', 'date_from', 'date_to', 'type'
        , 'isApplyAll', 'isDefault'];

    public function getType()
    {
        $typeList = config('system.payroll_type');
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

    public function customerGroups()
    {
        return $this->belongsToMany('App\Model\Entities\CustomerGroup', 'payroll_customer_group', 'payroll_id', 'customer_group_id');
    }

    public function formulas()
    {
        return $this->hasMany('App\Model\Entities\PayrollFormula', 'payroll_id');
    }

}