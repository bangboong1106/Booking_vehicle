<?php

namespace App\Repositories;

use App\Model\Entities\OrderFile;
use App\Repositories\Base\CustomRepository;
use Illuminate\Support\Facades\DB;

class OrderFileRepository extends CustomRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    function model()
    {
        return OrderFile::class;
    }

    public function getOrderFile($order_id, $order_status)
    {
        if ($order_id && $order_status)
            return $this->search([
                'order_id_eq' => $order_id,
                'order_status_eq' => $order_status,
                'del_flag' => 0
            ])->orderBy('ins_date')->get();
        return null;
    }

    public function getOrderFileWithOrderID($order_id)
    {
        if ($order_id)
            return $this->search([
                'order_id_eq' => $order_id,
            ])->get();
        return null;
    }

    public function getOrderFileWithFileIdAndOrderId($fileId, $order_id)
    {
        if ($fileId && $order_id)
            return $this->search([
                'order_id_eq' => $order_id,
                'file_id_eq' => $fileId,
            ])->first();
        return null;
    }

    public function getFileByOrderIdAndStatus($orderId, $order_status)
    {
        $result = DB::select("
             SELECT MAX(ofl.id) id, ofl.`order_status`, MAX(ofl.reason) reason, GROUP_CONCAT(f.file_id separator ';' ) as file_ids
             FROM `order_file` ofl
             left join `files` f on ofl.file_id = f.file_id
             where ofl.order_id = $orderId
              and ofl.order_status IN ($order_status) and ofl.del_flag = 0
             group by ofl.`order_status`
        ");
        return $result;
    }
}