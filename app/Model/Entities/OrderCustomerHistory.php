<?php

namespace App\Model\Entities;

use App\Model\Base\ModelSoftDelete;

class OrderCustomerHistory extends ModelSoftDelete
{
    protected $table = "order_customer_history";
    protected $fillable = ['order_customer_id', 'status', 'reason'];

}
