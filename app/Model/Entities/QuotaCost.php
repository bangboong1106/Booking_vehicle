<?php

namespace App\Model\Entities;

use App\Model\Base\ModelSoftDelete;

class QuotaCost extends ModelSoftDelete
{
    protected $table = "quota_cost";

    protected $_alias = 'quota_cost';
    protected $fillable = ['quota_id', 'receipt_payment_id', 'receipt_payment_name', 'amount', 'currency', 'type'];
}