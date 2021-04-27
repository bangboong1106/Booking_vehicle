<?php

namespace App\Repositories;

use App\Model\Entities\DistanceDailyReport;
use App\Repositories\Base\CustomRepository;
use App\Validators\DistanceDailyReportValidator;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DistanceDailyReportRepository extends CustomRepository
{
    function model()
    {
        return DistanceDailyReport::class;
    }

    public function validator()
    {
        return DistanceDailyReportValidator::class;
    }

    public function getUpTimeVehicleDaily($date, $gps_company_id)
    {
        $procedureName = 'proc_uptime_vehicle_daily';
        $sql = 'call ' . $procedureName . '(?, ?)';

        $query = DB::select($sql, array($date, $gps_company_id));
        return $query;
    }

    public function getVehicleRepairWarning()
    {
        $dayNumWarning = env("DAY_NUM_REPAIR_WARNING", 10);
        $distanceNumWarning = env("DISTANCE_NUM_REPAIR_WARNING", 100);
        if ($dayNumWarning == 0 && $distanceNumWarning == 0)
            return null;
        $dateExpire = Carbon::now()->subDays($dayNumWarning)->toDateString();
        $sql = "SELECT * FROM (
                    SELECT v.reg_no,v.repair_date,v.repair_distance,(SUM(vdr.distance)/1000) distance ,v.partner_id
                    FROM vehicle v 
                    LEFT JOIN vehicle_daily_report vdr ON v.vehicle_plate = vdr.vehicle_plate 
                    WHERE v.del_flag = 0 AND vdr.del_flag = 0
                                AND DATE_FORMAT(vdr.date,'%Y-%m-%d') > DATE_FORMAT(v.repair_date,'%Y-%m-%d')
                    GROUP BY v.reg_no
                    ) tmp WHERE 1=1 ";
        if ($dayNumWarning != 0) {
            $sql .= " AND tmp.distance > :distanceNumWarning ";
        }
        if ($dayNumWarning != 0) {
            $sql .= " OR DATE_FORMAT(tmp.repair_date,'%Y-%m-%d') >= DATE_FORMAT(:dateExpire,'%Y-%m-%d')";
        }

        $query = DB::select(DB::raw($sql)
            , array('distanceNumWarning' => $distanceNumWarning,
                'dateExpire' => $dateExpire))->toArray();

        return $query;
    }

    public function getVehicleRepairWarningByIds($ids)
    {
        if (empty($ids)) {
            return [];
        }

        $query = DB::table('vehicle')
            ->leftJoin('vehicle_daily_report', 'vehicle_daily_report.vehicle_plate', '=', 'vehicle.vehicle_plate')
            ->where('vehicle.del_flag', '=', 0)
            ->where('vehicle_daily_report.del_flag', '=', 0)
            ->whereIn('vehicle.id', $ids);
        return $query->get([
            'vehicle.id',
            'vehicle.reg_no',
            'vehicle.repair_date',
            'vehicle.repair_distance',
            DB::raw("(SUM(vehicle_daily_report.distance)/1000) as distance")
        ]);
    }

    public function getDistanceByRoutes($routeIds)
    {
        if (empty($routeIds)) {
            return [];
        }

        $query = DB::table('distance_daily_report as r')
            ->whereIn('r.route_id', $routeIds)
            ->groupBy('r.route_id')
            ->get([
                'r.route_id',
                DB::raw("SUM(r.distance) as distance"),
                DB::raw("SUM(r.distance_with_goods) as distance_with_goods"),
            ]);

        return $query;
    }
}