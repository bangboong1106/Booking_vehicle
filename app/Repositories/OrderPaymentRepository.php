<?php

namespace App\Repositories;

use App\Model\Entities\OrderPayment;
use App\Repositories\Base\CustomRepository;

class OrderPaymentRepository extends CustomRepository
{
    function model()
    {
        return OrderPayment::class;
    }

    public function getOrderPaymentWithOrderID($orderId)
    {
        try {
            if (!$orderId)
                return null;
            return $this->search([
                'order_id_eq' => $orderId
            ])->first();
        } catch (\Exception $e) {
            return null;
        }
    }
}