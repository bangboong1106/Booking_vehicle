<?php

namespace App\Model\Entities;

use App\Model\Base\ModelSoftDelete;

/**
 * @property mixed id
 */
class CustomerGroupCustomer extends ModelSoftDelete
{
    protected $table = "customer_group_customer";

    protected $_alias = 'customer_group_customer';

    protected $fillable = ['customer_group_id', 'customer_id'];
}