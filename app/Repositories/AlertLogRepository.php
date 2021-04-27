<?php

namespace App\Repositories;

use App\Common\AppConstant;
use App\Model\Entities\AlertLog;
use App\Repositories\Base\CustomRepository;
use DB;

class AlertLogRepository extends CustomRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    function model()
    {
        return AlertLog::class;
    }

    public function validator()
    {
        return \App\Validators\AlertLogValidator::class;
    }

    public function findByUserId($userId, $request)
    {
        $pageIndex = $request['pageIndex'];
        $pageSize = $request['pageSize'];
        $textSearch = $request['textSearch'];

        $countQuery = DB::table('alert_logs')
            ->where([
                ['del_flag', '=', '0']
            ])
            ->where(function ($query) use ($userId) {
                $query->where('alert_logs.alert_type', AppConstant::ALERT_LOG_TYPE_ALL)
                    ->orWhere('alert_logs.user_id', $userId);
            });
        $alertLogsQuery = DB::table('alert_logs')
            ->where([
                ['del_flag', '=', '0']
            ])
            ->where(function ($query) use ($userId) {
                $query->where('alert_logs.alert_type', AppConstant::ALERT_LOG_TYPE_ALL)
                    ->orWhere('alert_logs.user_id', $userId);
            });
        if (!empty($textSearch)) {
            $countQuery->where(function ($query) use ($textSearch) {
                $query->where('alert_logs.name', 'like', '%' . $textSearch . '%')
                    ->orWhere('alert_logs.title', 'like', '%' . $textSearch . '%');
            });
            $alertLogsQuery->where(function ($query) use ($textSearch) {
                $query->where('alert_logs.name', 'like', '%' . $textSearch . '%')
                    ->orWhere('alert_logs.title', 'like', '%' . $textSearch . '%');
            });
        }

        $count = $countQuery->count();
        $totalPage = 0;
        if (0 < $count) {
            $totalPage = (int)(($count + $pageSize - 1) / $pageSize);
        }
        $offset = ($pageIndex - 1) * $pageSize;

        $alertLogs = $alertLogsQuery->skip($offset)
            ->take($pageSize)
            ->get();
        $result = [
            'totalPage' => $totalPage,
            'alertLog' => $alertLogs
        ];

        return $result;
    }
}