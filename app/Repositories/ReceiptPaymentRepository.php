<?php

/**
 * Created by IntelliJ IDEA.
 * User: shine
 * Date: 10/6/18
 * Time: 12:22
 */

namespace App\Repositories;


use App\Model\Entities\ReceiptPayment;
use App\Repositories\Base\CustomRepository;
use App\Validators\ReceiptPaymentValidator;
use Illuminate\Support\Facades\DB;

class ReceiptPaymentRepository extends CustomRepository
{
    function model()
    {
        return ReceiptPayment::class;
    }

    public function validator()
    {
        return ReceiptPaymentValidator::class;
    }

    public function _isUsed($id)
    {
        $quotaCost = DB::table('quota_cost')
            ->where('quota_cost.del_flag', '=', '0')
            ->where('quota_cost.receipt_payment_id', '=', $id)
            ->first();
        if ($quotaCost)
            return true;

        $routeCost = DB::table('route_cost')
            ->where('route_cost.del_flag', '=', '0')
            ->where('route_cost.receipt_payment_id', '=', $id)
            ->first();
        if ($routeCost)
            return true;

        return false;
    }

    public function getAll($routeId, $type = null)
    {
        if($type == null){
            $type = config('constant.COST');
        }
        if (null == $routeId || empty($routeId)) {
            $search = DB::table('m_receipt_payment')
                ->where('del_flag', '=', '0')
                ->where('m_receipt_payment.is_display_driver', '=', 1)
                ->where('type', '=', $type)
                ->orderBy('is_system', 'DESC')
                ->orderBy('ins_date', 'DESC');
            return $search->get([
                'm_receipt_payment.id as costId',
                'm_receipt_payment.name as costName',
                'is_driver as isDriver',
                'amount as default_amount'
            ]);
        } else {
            $search = DB::table('m_receipt_payment')
                ->leftJoin('route_cost', function ($join) use ($routeId) {
                    $join->on('m_receipt_payment.id', '=', 'route_cost.receipt_payment_id')
                        ->on('route_cost.route_id', '=', DB::raw($routeId))
                        ->on('route_cost.del_flag', '=', DB::raw('0'));
                })
                ->where('m_receipt_payment.type', '=', $type)
                ->where('m_receipt_payment.is_display_driver', '=', 1)
                ->where('m_receipt_payment.del_flag', '=', '0')
                ->orderBy('route_cost.amount', 'DESC');

            return $search->get([
                'm_receipt_payment.id as costId',
                'm_receipt_payment.name as costName',
                'm_receipt_payment.is_driver as isDriver',
                'm_receipt_payment.amount as default_amount',
                'route_cost.amount as amount',
                'route_cost.amount_admin as amountAdmin',
                'route_cost.amount_driver as amountDriver',
                'route_cost.approved as approved',
                'route_cost.ins_date as insDate',
                'route_cost.description as description'
            ]);
        }
    }

    public function getAllExcel()
    {
        $search = DB::table('m_receipt_payment')
            ->orderBy('sort_order');
        // ->orderBy(DB::raw("REPLACE(m_receipt_payment.name,'-','')"));
        return $search->get(['m_receipt_payment.id as id', DB::raw("REPLACE(m_receipt_payment.name,'-','') as name")]);
    }
}
