<?php

namespace App\Repositories;

use App\Common\AppConstant;
use App\Model\Entities\NotificationLogClient;
use App\Repositories\Base\CustomRepository;
use DB;

class NotificationLogClientRepository extends CustomRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    function model()
    {
        return NotificationLogClient::class;
    }

    public function validator()
    {
        return \App\Validators\NotificationLogClientValidator::class;
    }

    public function readAllNotificationLogForCustomer($userId)
    {
        DB::table('notification_logs_client')->where('user_id', '=', $userId)->update(array('read_status' => AppConstant::NOTIFICATION_READ));
        return true;
    }

    public function getNotificationUnreadForUser($userId, $pageIndex, $isViewUnRead, $pageSize = 10)
    {
        if (!$userId)
            return [];
        $countUnreadQuery = DB::table('notification_logs_client')
            ->where([
                ['del_flag', '=', '0'],
                ['read_status', '=', '0']
            ])
            ->where('notification_logs_client.user_id', '=', $userId);

        $query = DB::table('notification_logs_client')
            ->where('del_flag', '=', '0')
            ->where('notification_logs_client.user_id', '=', $userId);

        if ($isViewUnRead == 'true') {
            $query->where('read_status', '=', 0);
        }
        $query->orderByDesc('ins_date');

        $count = $query->count();
        $countUnread = $countUnreadQuery->count();
        $totalPage = 0;
        if (0 < $count) {
            $totalPage = (int)(($count + $pageSize - 1) / $pageSize);
        }
        $offset = ($pageIndex - 1) * $pageSize;

        $notification = $query->skip($offset)
            ->take($pageSize)
            ->get();
        $result = [
            'totalCount' => $count,
            'countUnread' => $countUnread,
            'totalPage' => $totalPage,
            'items' => $notification
        ];

        return $result;
    }

}