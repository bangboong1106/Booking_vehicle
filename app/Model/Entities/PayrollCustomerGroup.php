<?php

namespace App\Model\Entities;

use App\Model\Base\ModelSoftDelete;

class PayrollCustomerGroup extends ModelSoftDelete
{
    protected $table = "payroll_customer_group";

    protected $_alias = 'payroll_customer_group';
    protected $fillable = ['payroll_id', 'customer_group_id'];

    public function customerGroup()
    {
        return $this->hasOne(CustomerGroup::class, 'id', 'customer_group_id');
    }
}