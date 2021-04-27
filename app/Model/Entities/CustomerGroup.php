<?php

namespace App\Model\Entities;

use App\Model\Base\ModelSoftDelete;

/**
 * @property mixed id
 */
class CustomerGroup extends ModelSoftDelete
{
    protected $table = "customer_group";

    protected $_alias = 'customer_group';

    protected $fillable = ['code', 'name'];

    public $customer_ids;
    protected $_detailNameField = 'code';

    public function customers()
    {
        return $this->belongsToMany('App\Model\Entities\Customer', 'customer_group_customer', 'customer_group_id', 'customer_id');
    }
}