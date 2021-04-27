<?php

namespace App\Model\Entities;

use App\Model\Base\ModelSoftDelete;

/**
 * @property mixed id
 */
class AdminUsersCustomerGroup extends ModelSoftDelete
{
    protected $table = "admin_users_customer_group";

    protected $_alias = 'admin_users_customer_group';

    protected $fillable = ['admin_user_id', 'customer_group_id'];
}