<?php

namespace App\Repositories;

use App\Model\Entities\Cost;
use App\Repositories\Base\CustomRepository;
use App\Validators\CostValidator;

class CostRepository extends CustomRepository
{
    function model()
    {
        return Cost::class;
    }

    public function validator()
    {
        return CostValidator::class;
    }

    public function getCost($receipt_payment_id, $amount)
    {
        $entity = null;
        if ($receipt_payment_id && $amount)
            $entity = $this->search([
                'receipt_payment_id_eq' => $receipt_payment_id,
                'amount_eq' => $amount
            ])->first();
        return $entity;
    }

    public function getCosts($costIds)
    {
        if (empty($costIds)) {
            return [];
        }
        return $this->search(['id_in' => $costIds], ['receipt_payment_id', 'receipt_payment_name', 'amount'])->get();
    }
}