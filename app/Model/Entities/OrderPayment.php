<?php

namespace App\Model\Entities;

use App\Model\Base\ModelSoftDelete;

class OrderPayment extends ModelSoftDelete
{
    protected $table = "order_payment";

    protected $_alias = 'order_payment';
    protected $fillable = ['order_id', 'payment_type', 'payment_user_id', 'goods_amount', 'vat', 'anonymous_amount'];

    public function paymentUser()
    {
        return $this->hasOne(AdminUserInfo::class, 'id', 'payment_user_id');
    }

    public function getPaymentType()
    {
        $paymentTypes = config('system.order_payment_type');

        if (empty($this->payment_type)) {
            return '';
        }

        return array_key_exists($this->payment_type, $paymentTypes) ? $paymentTypes[$this->payment_type] : '';
    }

}