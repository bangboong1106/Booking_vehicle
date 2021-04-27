<?php

namespace App\Repositories;

use App\Model\Entities\QuotaCost;
use App\Repositories\Base\CustomRepository;
use App\Validators\QuotaCostValidator;

class QuotaCostRepository extends CustomRepository
{
    function model()
    {
        return QuotaCost::class;
    }

    public function validator()
    {
        return QuotaCostValidator::class;
    }

    public function getCosts($quotaId)
    {
        if ($quotaId == null)
            return null;
        return $this->search([
            'quota_id_eq' => $quotaId
        ],['receipt_payment_id', 'receipt_payment_name', 'amount'])->get();
    }
}