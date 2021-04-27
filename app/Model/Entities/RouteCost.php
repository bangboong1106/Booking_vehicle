<?php

namespace App\Model\Entities;

use App\Model\Base\ModelSoftDelete;

class RouteCost extends ModelSoftDelete
{
    protected $table = "route_cost";

    protected $_alias = 'route_cost';
    protected $fillable = ['route_id', 'receipt_payment_id', 'receipt_payment_name', 'amount', 'currency', 'type',
        'amount_admin', 'amount_driver', 'description'];

    public function receiptPayment()
    {
        return $this->hasOne(ReceiptPayment::class, 'id', 'receipt_payment_id');
    }
}
