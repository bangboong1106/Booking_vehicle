<?php

namespace App\Repositories;

use App\Model\Entities\VehicleFile;
use App\Repositories\Base\CustomRepository;
use Carbon\Carbon;
use DB;

class VehicleFileRepository extends CustomRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    function model()
    {
        return VehicleFile::class;
    }

    public function getVehicleFile($vehicle_id, $vehicle_config_file_id)
    {
        if ($vehicle_id && $vehicle_config_file_id)
            return $this->search([
                'vehicle_id_eq' => $vehicle_id,
                'vehicle_config_file_id_eq' => $vehicle_config_file_id,
            ])->get();
        return null;
    }

    public function getVehicleFileWithVehicleID($vehicle_id)
    {
        if ($vehicle_id)
            return $this->search([
                'vehicle_id_eq' => $vehicle_id,
            ])->get();
        return null;
    }

    public function getVehicleFileWarning()
    {
        $dayNumWarning = env("DAY_NUM_WARNING", 30);
        $dateExpire = Carbon::now()->addDays($dayNumWarning)->toDateString();

        $query = DB::table('vehicle_file')
            ->join('vehicle', 'vehicle_file.vehicle_id', '=', 'vehicle.id')
            ->join('vehicle_config_file', 'vehicle_file.vehicle_config_file_id', '=', 'vehicle_config_file.id')
            ->where('vehicle_file.del_flag', '=', 0)
            ->where('vehicle_file.expire_date', '=', $dateExpire)
            ->where('vehicle.del_flag', '=', 0)
            ->where('vehicle_config_file.del_flag', '=', 0);
        return $query->get([
            'vehicle.reg_no as reg_no',
//            'vehicle_file.expire_date as expire_date',
            DB::raw('DATE_FORMAT(vehicle_file.expire_date, "%d/%m/%Y") as expire_date'),
            'vehicle_config_file.file_name as file_name',
            'vehicle.partner_id'
        ])->toArray();
    }

    public function getVehicleFileWarningByIds($ids)
    {
        if (empty($ids)) {
            return [];
        }

        $query = DB::table('vehicle_file')
            ->join('vehicle', 'vehicle_file.vehicle_id', '=', 'vehicle.id')
            ->join('vehicle_config_file', 'vehicle_file.vehicle_config_file_id', '=', 'vehicle_config_file.id')
            ->where('vehicle_file.del_flag', '=', 0)
            ->where('vehicle.del_flag', '=', 0)
            ->where('vehicle_config_file.del_flag', '=', 0)
            ->whereIn('vehicle.id', $ids);
        return $query->get([
            'vehicle.id',
            'vehicle.reg_no as reg_no',
            DB::raw('DATE_FORMAT(vehicle_file.expire_date, "%d/%m/%Y") as expire_date'),
            'vehicle_config_file.file_name as file_name'
        ]);
    }
}