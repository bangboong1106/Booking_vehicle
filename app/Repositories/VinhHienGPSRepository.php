<?php

namespace App\Repositories;

use App\Model\Entities\VinhHienGPS;
use App\Repositories\Base\CustomRepository;
use DB;

class VinhHienGPSRepository extends CustomRepository
{
    protected $_fieldsSearch = ['vehicle_plate', 'datetime', 'lat', 'lon', 'address', 'current_total_km'];

    /**
     * Specify Model class name
     *
     * @return string
     */
    function model()
    {
        return VinhHienGPS::class;
    }

    public function validator()
    {
        return \App\Validators\VinhHienGPSValidator::class;
    }

    public function getDistanceByTime($vehiclePlate, $from, $to)
    {

    }

    public function getCurrentTotalKmByTime($vehiclePlate, $time)
    {
        $query = DB::table('vinhhien_gps')
            ->where('vehicle_plate', '=', $vehiclePlate)
            ->whereRaw("ABS(TIMESTAMPDIFF(MINUTE, ?, datetime)) <= 15", [$time])
            ->orderByRaw("ABS(TIMESTAMPDIFF(MINUTE, ?, datetime))", [$time])
            ->limit(1);
        return $query->first();
    }

    public function deleteGpsData() {
        $days = env('VINHHIEN_DAY_NUM_STORE_DATA', 3);
        DB::table('vinhhien_gps')->whereRaw("ABS(TIMESTAMPDIFF(DAY, NOW(), datetime)) >= ?", [$days])->delete();
    }
}
