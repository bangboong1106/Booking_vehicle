<?php

namespace App\Repositories;

use App\Model\Entities\RouteCost;
use App\Repositories\Base\CustomRepository;
use App\Validators\RouteCostValidator;

class RouteCostRepository extends CustomRepository
{
    function model()
    {
        return RouteCost::class;
    }

    public function validator()
    {
        return RouteCostValidator::class;
    }

    public function getCosts($routeId)
    {
        if ($routeId == null)
            return null;
        $costs = $this->search([
            'route_id_eq' => $routeId
        ])->with('receiptPayment')->get();
        return $costs;
    }

    public function getCost($routeId, $receiptPaymentId)
    {
        if ($routeId == null || $receiptPaymentId == null)
            return null;
        $cost = $this->search([
            'route_id_eq' => $routeId,
            'receipt_payment_id_eq' => $receiptPaymentId
        ])->first();
        return $cost;
    }
}