<?php

namespace App\Repositories;

use App\Model\Entities\NotificationLogDriver;
use App\Repositories\Base\CustomRepository;
use DB;

class NotificationLogDriverRepository extends CustomRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    function model()
    {
        return NotificationLogDriver::class;
    }

    public function validator()
    {
        return \App\Validators\NotificationLogDriverValidator::class;
    }

    public function countUnreadLogByDriverId($driverId) {
        if (!$driverId)
            return -1;

        $countQuery = DB::table('notification_logs_driver')
            ->where([
                ['del_flag', '=', '0']
            ])
            ->where([
                ['read_status', '=', '0']
            ])
            ->where(function ($query) use ($driverId) {
                $query->where('notification_logs_driver.driver_id', $driverId);
            });
        return $countQuery->count();
    }

    public function findByDriverId($driverId, $request)
    {
        if (!$driverId || !$request)
            return [];

        $pageIndex = $request['pageIndex'];
        $pageSize = $request['pageSize'];
        $textSearch = $request['textSearch'];

        $countQuery = DB::table('notification_logs_driver')
            ->where([
                ['del_flag', '=', '0']
            ])
            ->where(function ($query) use ($driverId) {
                $query->where('notification_logs_driver.driver_id', $driverId);
            });

        $driverLogsQuery = DB::table('notification_logs_driver')
            ->where([
                ['del_flag', '=', '0']
            ])
            ->where(function ($query) use ($driverId) {
                $query->where('notification_logs_driver.driver_id', $driverId);
            });
        if (!empty($textSearch)) {
            $countQuery->where(function ($query) use ($textSearch) {
                $query->where('notification_logs_driver.title', 'like', '%' . $textSearch . '%')
                    ->orWhere('notification_logs_driver.message', 'like', '%' . $textSearch . '%');
            });
            $driverLogsQuery->where(function ($query) use ($textSearch) {
                $query->where('notification_logs_driver.title', 'like', '%' . $textSearch . '%')
                    ->orWhere('notification_logs_driver.message', 'like', '%' . $textSearch . '%');
            });
        }

        $count = $countQuery->count();
        $totalPage = 0;
        if (0 < $count) {
            $totalPage = (int)(($count + $pageSize - 1) / $pageSize);
        }
        $offset = ($pageIndex - 1) * $pageSize;

        $countUnreadQuery = $countQuery;
        $countUnreadQuery->where([
            ['read_status', '=', '0']
        ]);

        $driverLogsQuery->orderBy('notification_logs_driver.ins_date', 'DESC');
        $driverLogs = $driverLogsQuery->skip($offset)
            ->take($pageSize)
            ->get();
        $result = [
            'totalPage' => $totalPage,
            'items' => $driverLogs,
            'totalCount' => $count,
            'countUnread' => $countUnreadQuery->count()
        ];

        return $result;
    }

    public function findByDriverIdAndId($driverId, $id)
    {
        $entity = null;
        if ($driverId && $id)
            $entity = $this->search([
                'id_eq' => $id,
                'driver_id_eq' => $driverId
            ])->first();
        return $entity;
    }

    public function countUnreadNotification($driverId)
    {
        if (!$driverId)
            return 0;
        $countQuery = DB::table('notification_logs_driver')
            ->where([
                ['del_flag', '=', '0'],
                ['read_status', '=', '0']
            ])
            ->where(function ($query) use ($driverId) {
                $query->where('notification_logs_driver.driver_id', $driverId);
            });
        return $countQuery->count();
    }

    public function updateReadAllByDriverId($driverId)
    {
        DB::table('notification_logs_driver')
            ->where([
                ['del_flag', '=', '0'],
                ['driver_id', '=', $driverId]
            ])
            ->update(['read_status' => 1]);
    }
}