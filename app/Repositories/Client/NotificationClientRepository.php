<?php

/**
 * Created by IntelliJ IDEA.
 * User: shine
 * Date: 10/6/18
 * Time: 12:22
 */

namespace App\Repositories\Client;


use App\Common\AppConstant;
use App\Model\Entities\Ward;
use App\Repositories\NotificationLogRepository;
use App\Repositories\WardRepository;
use App\Validators\WardValidator;
use DB;

class NotificationClientRepository extends NotificationLogRepository
{

    // API lấy thông tin địa điểm
    // CreatedBy nlhoang 19/05/2020
    public function getDataList($clientID, $customerID, $request)
    {
        $pageSize = $request['pageSize'];
        $pageIndex = $request['pageIndex'];

        $query = DB::table('notification_logs_client')
            ->where([
                ['del_flag', '=', '0'],
            ])
            ->where(function ($query) use ($customerID) {
                $query->where('notification_logs_client.user_id', $customerID);
            })->orderBy('ins_date', "DESC");

        $count = $query->count();
        $totalPage = 0;
        if (0 < $count) {
            $totalPage = (int) (($count + $pageSize - 1) / $pageSize);
        }
        $offset = ($pageIndex - 1) * $pageSize;

        $query->skip($offset)->take($pageSize);
        $items = $query->get(['*']);

        return [
            'totalPage' => $totalPage,
            'totalCount' => $count,
            'items' => $items
        ];
    }

    // API đánh dấu trạng thái đã đọc
    // CreatedBy nlhoang 04/06/2020
    public function markRead($userId, $id)
    {
        if ($id != 0) {
            $notificationLog = $this->findFirstOrNew(['id' => $id]);
            if ($notificationLog != null) {
                $notificationLog->read_status = AppConstant::NOTIFICATION_READ;
                $notificationLog->save();
            }
        } else {
            DB::table('notification_logs_client')
                ->where([
                    ['del_flag', '=', '0'],
                    ['user_id', '=', $userId]
                ])
                ->update(['read_status' => 1]);
        }
    }

    // API lấy tổng thông báo chưa đọc
    // CreatedBy nlhoang 16/06/2020
    public function getTotalUnread($userId)
    {
        $query = DB::table('notification_logs_client')
            ->where([
                ['del_flag', '=', '0'],
                'read_status' => 0
            ])->where(function ($query) use ($userId) {
                $query->where('notification_logs_client.user_id', $userId);
            });

        $count = $query->count();
        return $count;
    }
}
