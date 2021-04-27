<?php

namespace App\Repositories;

use App\Model\Entities\DriverFile;
use App\Repositories\Base\CustomRepository;

class DriverFileRepository extends CustomRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    function model()
    {
        return DriverFile::class;
    }

    public function getDriverFile($driver_id, $driver_config_file_id)
    {
        if ($driver_id && $driver_config_file_id)
            return $this->search([
                'driver_id_eq' => $driver_id,
                'driver_config_file_id_eq' => $driver_config_file_id,
            ])->get();
        return null;
    }

    public function getDriverFileWithDriverID($driver_id)
    {
        return $this->search([
            'driver_id_eq' => $driver_id,
        ])->get();
    }
}