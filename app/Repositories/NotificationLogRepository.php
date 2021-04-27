<?php

namespace App\Repositories;

use App\Common\AppConstant;
use App\Model\Entities\NotificationLog;
use App\Repositories\Base\CustomRepository;
use DB;

class NotificationLogRepository extends CustomRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    function model()
    {
        return NotificationLog::class;
    }

    public function validator()
    {
        return \App\Validators\NotificationLogValidator::class;
    }

    public function getNotificationForUser($userId, $pageSize = 5)
    {
        if (!$userId)
            return [];
        // TODO: Hardcode
        $pageIndex = 1;

        $countQuery = DB::table('notification_logs')
            ->where([
                ['del_flag', '=', '0']
            ])
            ->where(function ($query) use ($userId) {
                $query->where('notification_logs.type', AppConstant::NOTIFICATION_TYPE_ALL)
                    ->orWhere('notification_logs.user_id', $userId);
            });
        $countUnreadQuery = DB::table('notification_logs')
            ->where([
                ['del_flag', '=', '0'],
                ['read_status', '=', '0']
            ])
            ->where(function ($query) use ($userId) {
                $query->where('notification_logs.type', AppConstant::NOTIFICATION_TYPE_ALL)
                    ->orWhere('notification_logs.user_id', $userId);
            });
        $query = DB::table('notification_logs')
            ->where([
                ['del_flag', '=', '0']
            ])
            ->where(function ($query) use ($userId) {
                $query->where('notification_logs.type', AppConstant::NOTIFICATION_TYPE_ALL)
                    ->orWhere('notification_logs.user_id', $userId);
            })
            ->orderByDesc('ins_date');

        $count = $countQuery->count();
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
            'count' => $count,
            'totalPage' => $totalPage,
            'countUnread' => $countUnread,
            'notification' => $notification
        ];

        return $result;
    }

    public function getNotificationUnreadForUser($userId, $pageIndex, $isViewUnRead, $pageSize = 10)
    {
        if (!$userId)
            return [];
        $countUnreadQuery = DB::table('notification_logs')
            ->where([
                ['del_flag', '=', '0'],
                ['read_status', '=', '0']
            ])
            ->where(function ($query) use ($userId) {
                $query->where('notification_logs.type', AppConstant::NOTIFICATION_TYPE_ALL)
                    ->orWhere('notification_logs.user_id', $userId);
            });

        $query = DB::table('notification_logs')
            ->where('del_flag', '=', '0')
            ->where(function ($query) use ($userId) {
                $query->where('notification_logs.type', AppConstant::NOTIFICATION_TYPE_ALL)
                    ->orWhere('notification_logs.user_id', $userId);
            });
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
            'count' => $count,
            'countUnread' => $countUnread,
            'totalPage' => $totalPage,
            'notification' => $notification
        ];

        return $result;
    }

    public function getNotificationLogs($userId, $page, $perPage = 10)
    {
        if (!$userId)
            return null;

        return $entity = $this->search([
            'user_id_eq' => $userId,
        ])->orderByDesc('ins_date')->paginate($perPage, ['*'], 'page', $page);
    }

    public function getNotificationLogById($userId, $id)
    {
        if (!$userId || !$id)
            return null;

        return $entity = $this->search([
            'user_id_eq' => $userId,
            'id_eq' => $id,
        ])->first();
    }
}