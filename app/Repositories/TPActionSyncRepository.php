<?php

namespace App\Repositories;

use App\Model\Entities\TPActionSync;
use App\Repositories\Base\CustomRepository;
use App\Validators\TPActionSyncValidator;
use Illuminate\Support\Facades\DB;

class TPActionSyncRepository extends CustomRepository
{
    function model()
    {
        return TPActionSync::class;
    }

    public function validator()
    {
        return TPActionSyncValidator::class;
    }

    public function getActionNotSync()
    {
        return $this->search([
            'sended_lt' => 3
        ])->orderBy('id', 'asc')->get();
    }

    public function getActionNotSyncByPartner($partnerName)
    {
        return $this->search([
            'sended_lt' => 3,
            'partner_name_eq' => $partnerName
        ])->orderBy('id', 'asc')->get();
    }

    public function getListPartnerInfo()
    {
        return DB::table('3p_api_info')
            ->where('3p_api_info.del_flag', '=', 0)
            ->get()->pluck('partner_name', 'customer_id');
    }

    public function saveActionSync($order)
    {
        $partnerInfos = $this->getListPartnerInfo();
        if ($order && $partnerInfos && isset($partnerInfos[$order->customer_id])) {
            $actionSync = $this->findFirstOrNew([]);
            $actionSync->partner_name = $partnerInfos[$order->customer_id];
            $actionSync->order_id = $order->id;
            $actionSync->order_no = $order->order_no;
            $actionSync->order_code = $order->order_code;
            $actionSync->bill_no = $order->bill_no;
            $actionSync->status = $order->status;
            $actionSync->ETD_date_reality = $order->ETD_date_reality;
            $actionSync->ETD_time_reality = $order->ETD_time_reality;
            $actionSync->ETA_date_reality = $order->ETA_date_reality;
            $actionSync->ETA_time_reality = $order->ETA_time_reality;
            $actionSync->amount = $order->amount;
            $actionSync->note = $order->remark;
            $actionSync->save();

        }
    }

    public function triggerActionSync($orderOld, $orderNew)
    {
        if ($orderOld == null && $orderNew != null) {
            $this->saveActionSync($orderNew);
        } else if ($orderOld != null && $orderNew != null) {
            if ($orderOld->order_no != $orderNew->order_no || $orderOld->order_code != $orderNew->order_code
                || $orderOld->bill_no != $orderNew->bill_no || $orderOld->status != $orderNew->status
                || $orderOld->amount != $orderNew->amount || $orderOld->note != $orderNew->note
                || $orderOld->ETD_date_reality != $orderNew->ETD_date_reality || $orderOld->ETD_time_reality != $orderNew->ETD_time_reality
                || $orderOld->ETA_date_reality != $orderNew->ETA_date_reality || $orderOld->ETA_time_reality != $orderNew->ETA_time_reality) {
                $this->saveActionSync($orderNew);
            }
        }
    }
}