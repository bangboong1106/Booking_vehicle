<?php

namespace App\Model\Entities;

use App\Model\Base\ModelSoftDelete;

class Cost extends ModelSoftDelete
{
    protected $table = "cost";

    protected $_alias = 'cost';
    protected $fillable = ['receipt_payment_id', 'receipt_payment_name', 'amount', 'currency_id', 'type'];
    protected $_detailNameField = 'receipt_payment_name';
}