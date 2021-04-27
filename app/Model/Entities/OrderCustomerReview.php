<?php

namespace App\Model\Entities;

use App\Model\Base\ModelSoftDelete;

class OrderCustomerReview extends ModelSoftDelete
{
    protected $table = "order_customer_review";

    protected $_alias = 'order_customer_review';
    protected $fillable = ['order_id', 'point', 'description'];
}